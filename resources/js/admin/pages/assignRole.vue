<template>
       <div>
       <div class="content">
			<div class="container-fluid">

				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					Role Management 
                    
                    <Select v-model="data.role_id" placeholder="Select Admin Role" class="space" style="width:300px;">
                        <Option :value="r.id" v-for="(r, i) in roles" :key="i" v-if="roles.length">{{ r.roleName }}</Option>
                    </Select>
                    
					<div class="_overflow _table_div">
						<table class="_table">
							<tr>
								<th>Resource name</th>
								<th>Read</th>
								<th>Write</th>
								<th>Update</th>
								<th>Delete</th>
							</tr>
							<tr v-for="(r, i) in resources" :key="i">
								<td>{{ r.resourceName }}</td>
								<td><Checkbox v-model="r.read"></Checkbox></td>
								<td><Checkbox v-model="r.write"></Checkbox></td>
								<td><Checkbox v-model="r.update"></Checkbox></td>
								<td><Checkbox v-model="r.delete"></Checkbox></td>
							</tr>
						</table>
					</div>
				</div>

	

			</div>
		</div>
    </div>
</template>


<script>

export default{

	data(){
		return {
			data : {
				roleName: '',
                role_id: null
			},
            roles: [],
			resources: [
                {resourceName: 'Tags', read:false, write:false, update:false, delete:false, name:'tags'},
                {resourceName: 'Category', read:false, write:false, update:false, delete:false, name:'category'},
                {resourceName: 'Admin Users', read:false, write:false, update:false, delete:false, name:'adminusers'},
                {resourceName: 'Role', read:false, write:false, update:false, delete:false, name:'role'},
                {resourceName: 'Assign Role', read:false, write:false, update:false, delete:false, name:'assignRole'},
                {resourceName: 'Home', read:false, write:false, update:false, delete:false, name:'home'},
            ],
		}
	},

	methods  : {
		 
	},

	async created(){
		const res = await this.callApi('get', 'app/get_roles')
		if(res.status==200 ){
			this.roles = res.data
		}else{
			this.swr()
		}
	},	
}
</script>