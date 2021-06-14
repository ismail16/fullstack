<template>
       <div>
       <div class="content">
			<div class="container-fluid">

				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Role Management <Button size="small" @click="addModal=true">Add New Role<Icon type="md-add" /></Button></p>
					<div class="_overflow _table_div">
						<table class="_table">
							<tr>
								<th>ID</th>
								<th>Role Type</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
							<tr v-for="(role, i) in roles" :key="i">
								<td>{{ role.id}}</td>
								<td class="_table_name">{{ role.roleName}}</td>
								<td>{{ role.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(role, i)">Edit</Button>
									<Button type="error" size="small" @click="showDeletingModal(role, i)" :loading="role.isDeleting">Delete</Button>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<!-- add tag modal -->
				<Modal
					v-model="addModal"
					title="Add Role"
					:mask-closable = "false"
					:closable = "false"
					>
					<Input v-model="data.roleName" placeholder="Role name"/>

					<div slot="footer">
						<Button type="default" @click="addModal=false">Close</Button>
						<Button type="primary" @click="add" :disabled="isAdding" :loading="isAdding">{{ isAdding ? 'Adding..' : "Add Role" }}</Button>
					</div>
				</Modal>

				<!-- Edit tag modal -->
				<Modal
					v-model="editModal"
					title="Edit Role"
					:mask-closable = "false"
					:closable = "false"
					>
					<Input v-model="editData.roleName" placeholder="Edit role name"/>

					<div slot="footer">
						<Button type="default" @click="editModal=false">Close</Button>
						<Button type="primary" @click="editRole" :disabled="isAdding" :loading="isAdding">{{ isAdding ? 'Editing..' : "Edit role" }}</Button>
					</div>
				</Modal>


				<!-- Delete tag modal -->
				<!-- <Modal v-model="showDeleteModal" width="360">
					<p slot="header" style="color:#f60;text-align:center">
						<Icon type="ios-information-circle"></Icon>
						<span>Delete confirmation</span>
					</p>
					<div style="text-align:center">
						<p>Are you sure you want to delete this tag ?</p>
					</div>
					<div slot="footer">
						<Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteTag">Delete</Button>
					</div>
				</Modal> -->
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
				roleName: '',
			},

			addModal: false,
			editModal: false,
			isAdding: false,
			roles: [],
			editData : {
				roleName: ''
			},

			index: -1,
			showDeleteModal : false,
			isDeleting: false,
			deleteItem: {},
			deletingIndex: -1
		}
	},

	methods  : {
		async add(){
			if(this.data.roleName.trim()=='') return this.e('Role name is Required!')
			const res = await this.callApi('post', 'app/create_role', this.data );
			if(res.status==201 ){
				this.roles.unshift(res.data)
				this.s('Role has been added Successfully!')
				this.addModal = false
				this.data.roleName = ''
			}else{
				if(res.status==422){
					if(res.data.errors.roleName){
						this.i(res.data.errors.roleName[0])
					}
				}else{
					this.swr()
				}
			}
		},
		async editRole(){
			if(this.editData.roleName.trim()=='') return this.e('Role name is Required!')
			const res = await this.callApi('post', 'app/edit_role', this.editData );
			if(res.status==200 ){
				this.roles[this.index].roleName = this.editData.roleName
				this.s('Role has been edited Successfully!')
				this.editModal = false
			}else{
				if(res.status==422){
					if(res.data.errors.roleName){
						this.i(res.data.errors.roleName[0])
					}
				}else{
					this.swr()
				}
			}
		},

		showEditModal(role, index){
			let obj = {
				id : role.id,
				roleName : role.roleName
			}
			this.editData = obj,
			this.editModal = true,
			this.index = index
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

			this.editData = category
			this.editModal = true
			this.index = index
			this.isEditingItem = true
		},

		// async deleteTag(){
		// 	this.isDeleting = true
		// 	const res = await this.callApi('post', 'app/delete_tag', this.deleteItem );
		// 	if(res.status==200 ){
		// 		this.tags.splice(this.deletingIndex, 1)
		// 		this.s('Tag has been deleted successfully')
		// 	}else{
		// 		this.swr()
		// 	}
		// 	this.isDeleting = false
		// 	this.showDeleteModal = false
		// },

		showDeletingModal(tag, i){
			const deleteModalObj ={
				showDeleteModal : true,
				deleteUrl : 'app/delete_role',
				data : tag,
				deletingIndex: i,
				isDeleted: false
			}
			this.$store.commit('setDeletingModalObj', deleteModalObj)
		}
		 
	},

	async created(){
		const res = await this.callApi('get', 'app/get_roles')
		if(res.status==200 ){
			this.roles = res.data
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
				this.roles.splice(obj.deletingIndex, 1)
			}
		}
	}
}
</script>