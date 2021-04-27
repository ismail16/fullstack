<template>
       <div>
       <div class="content">
			<div class="container-fluid">

				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Categories <Button size="small" @click="addModal=true">Add New <Icon type="md-add" /></Button></p>
					<div class="_overflow _table_div">
						<table class="_table">
							<tr>
								<th>ID</th>
								<th>Icon Image</th>
								<th>Category Name</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
							<tr v-for="(category, i) in categoryLists" :key="i">
								<td>{{ category.id}}</td>
								<td class="table_image">
									<img :src="category.iconImage" alt="">
								</td>
								<td class="_table_name">{{ category.categoryName}}</td>
								<td>{{ category.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(category, i)">Edit</Button>
									<Button type="error" size="small" @click="showDeletingModal(category, i)" :loading="category.isDeleting">Delete</Button>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<!-- add tag modal -->
				<Modal
					v-model="addModal"
					title="Add category"
					:mask-closable = "false"
					:closable = "false"
					>
					<Input v-model="data.categoryName" placeholder="Add category name"/>
                    <div class="space">
                        <Upload
                            ref="upload"
                            type="drag"
                            :headers="{'x-csrf-token' : token, 'X-Requested-With' : 'XMLHttpRequest'}"
							:on-success="handleSuccess"
							:on-error="handleError"
							:format="['jpg','jpeg','png']"
							:max-size="2048"
							:on-format-error="handleFormatError"
							:on-exceeded-size="handleMaxSize"
                            action="/app/upload">
                            <div style="padding: 20px 0">
                                <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
                                <p>Click or drag files here to upload</p>
                            </div>
                        </Upload>
						<div class="demo-upload-list" v-if="data.iconImage">
							<img :src="`${data.iconImage}`" alt="">
							<div class="demo-upload-list-cover">
								<Icon type="ios-trash-outline" @click="deleteImage"></Icon>
							</div>
						</div>
                    </div>

					<div slot="footer">
						<Button type="default" @click="addModal=false">Close</Button>
						<Button type="primary" @click="addCategory" :disabled="isAdding" :loading="isAdding">{{ isAdding ? 'Adding..' : "Add Category" }}</Button>
					</div>
				</Modal>

				<!-- Edit tag modal -->
				<Modal
					v-model="editModal"
					title="Edit Category"
					:mask-closable = "false"
					:closable = "false"
					>
					<Input v-model="editData.categoryName" placeholder="Category name"/>
                    <div class="space">
                        <Upload v-show="isIconImageNew"
                            ref="editDataUpload"
                            type="drag"
                            :headers="{'x-csrf-token' : token, 'X-Requested-With' : 'XMLHttpRequest'}"
							:on-success="handleSuccess"
							:on-error="handleError"
							:format="['jpg','jpeg','png']"
							:max-size="2048"
							:on-format-error="handleFormatError"
							:on-exceeded-size="handleMaxSize"
                            action="/app/upload">
                            <div style="padding: 20px 0">
                                <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
                                <p>Click or drag files here to upload</p>
                            </div>
                        </Upload>
						<div class="demo-upload-list" v-if="editData.iconImage">
							<img :src="`${editData.iconImage}`" alt="">
							<div class="demo-upload-list-cover">
								<Icon type="ios-trash-outline" @click="deleteImage(false)"></Icon>
							</div>
						</div>
					</div>

					<div slot="footer">
						<Button type="default" @click="closeEditModal">Close</Button>
						<Button type="primary" @click="editCategory" :disabled="isAdding" :loading="isAdding">{{ isAdding ? 'Editing..' : "Edit Category" }}</Button>
					</div>
				</Modal>
				<deleteModal></deleteModal>
			</div>
		</div>
    </div>
</template>


<script>
import deleteModal from '../components/deleteModal.vue'
import { mapGetters } from 'vuex'
export default{

	data(){
		return {
			data : {
				categoryName:'',
				iconImage: ''
			},

			addModal: false,
			editModal: false,
			isAdding: false,
			categoryLists: [],
			editData : {
				categoryName:'',
				iconImage: ''
			},

			index: -1,
			showDeleteModal : false,
			isDeleting: false,
			deleteItem: {},
			deletingIndex: -1,
            token: '',
			isIconImageNew: false,
			isEditingItem: false

		}
	},

	methods  : {
		async addCategory(){
			if(this.data.categoryName.trim()=='') return this.e('Category name is Required!')
			if(this.data.iconImage.trim()=='') return this.e('Icon Image is Required!')
			this.data.iconImage = `${this.data.iconImage}`
			const res = await this.callApi('post', 'app/create_category', this.data );
			if(res.status==201 ){
				this.categoryLists.unshift(res.data)
				this.$refs.upload.clearFiles()
				this.s('Category has been added Successfully!')
				this.addModal = false
				this.data.categoryName = ''
				this.data.iconImage = ''
			}else{
				if(res.status==422){
					if(res.data.errors.categoryName){
						this.i(res.data.errors.categoryName[0])
					}
					if(res.data.errors.iconImage){
						this.i(res.data.errors.iconImage[0])
					}
				}else{
					this.swr()
				}
			}
		},

		async editCategory(){
			if(this.editData.categoryName.trim()=='') return this.e('Category name is Required!')
			if(this.editData.iconImage.trim()=='') return this.e('Icon Image is Required!')
			const res = await this.callApi('post', 'app/edit_category', this.editData );
			if(res.status==200 ){
				this.categoryLists[this.index].categoryName = this.editData.categoryName
				this.s('Category has been edied Successfully!')
				this.editModal = false
			}else{
				if(res.status==422){
					if(res.data.errors.categoryName){
						this.i(res.data.errors.categoryName[0])
					}
					if(res.data.errors.iconImage){
						this.i(res.data.errors.iconImage[0])
					}
				}else{
					this.swr()
				}
			}
		},

		showEditModal(category, index){
			// let obj = {
			// 	id : category.id,
			// 	tagName : category.tagName
			// }
			this.editData = category
			this.editModal = true
			this.index = index
			this.isEditingItem = true
		},

		showDeletingModal(category, i){
			const deleteModalObj = {
				showDeleteModal : true,
				deleteUrl : 'app/delete_category',
				data : category,
				deletingIndex: i,
				isDeleted: false
			}
			this.$store.commit('setDeletingModalObj', deleteModalObj)
		},

		handleSuccess (res, file) {
			res = `/upload/${res}`
			if(this.isEditingItem){
				return this.editData.iconImage = res
			}
			this.data.iconImage = res
		},
		handleError (res, file) {
			this.$Notice.warning({
				title: 'The file format is incorrect',
				desc: `${file.errors.file.length ? file.errors.file[0] : 'Something went wrong !'}`
			});
		},

		handleFormatError (file) {
			this.$Notice.warning({
				title: 'The file format is incorrect',
				desc: 'File format of ' + file.name + ' is incorrect, please select jpg or png.'
			});
		},
		handleMaxSize (file) {
			this.$Notice.warning({
				title: 'Exceeding file size limit',
				desc: 'File  ' + file.name + ' is too large, no more than 2M.'
			});
		},

		async deleteImage(isAdd=true){
			let image
			if(!isAdd){
				//editing
				this.isIconImageNew = true
				image = this.editData.iconImage
				this.editData.iconImage = ''
				this.$refs.editDataUpload.clearFiles()
			}else{
				image = this.data.iconImage
				this.data.iconImage = ''
				this.$refs.upload.clearFiles()
			}
			
			const res = await this.callApi('post', 'app/delete_image', {imageName: image})
			if(res.status!=200){
				this.data.iconImage = image
				this.swr()
			}
		},

		closeEditModal(){
			this.isEditingItem = false
			this.editModal = false
		}
		 
	},

	async created(){
        this.token = window.Laravel.csrfToken
		const res = await this.callApi('get', 'app/get_category')
		if(res.status==200 ){
			this.categoryLists = res.data
		}else{
			this.swr()
		}
	},

	components :{
		deleteModal
	},

	
	computed:{
		...mapGetters(['getDeleteModalObj'])
	},

	watch : {
		getDeleteModalObj(obj){
			if(obj.isDeleted){
				this.categoryLists.splice(obj.deletingIndex, 1)
			}
		}
	}
}
</script>