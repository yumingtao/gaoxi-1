$(function(){	
		$.validator.setDefaults({
			submitHandler:function() {
				$('#form1').submit();
			}
		});

		$("#form1").validate({
			rules:{
				username:{
					required:true,
					minlength:2
				},
				password:{
					required:true,
					minlength:5,
					maxlength:12
				},
				passwordConfirm:{
					required:true,
					minlength:5,
					maxlength:12,
					equalTo:"#password" 
				},
				nickname:{
					required:true,
					minlength:2
				},
				become:'required',
				salaryss:{
					number:true,
				},
				salaryzz:{
					number:true,
				},
				housing:{
					number:true,
				},
				traffic:{
					number:true,
				},
				catering:{
					number:true,
				},
				phone:{
					number:true,
				},
				department:'required',
				post:'required',
				name:{
					required:true,
					minlength:2
				},
				sex:'required',
				identity_card:{
					required:true,
				},
				mobile:{
					required:true,
					number:true,
					minlength:11,
					maxlength:11
				},
				education:'required',
				phone_ugy:{
					number:true,
					minlength:11,
					maxlength:11
				},
				bankaddress:'required',
				bank_account:'required'
			},
			messages:{				
				username:{
					required:'请输入用户名',
					minlength:'用户名至少2个字符'
				},		
				password:{
					required:'请输入密码',
					minlength:'密码长度应在5-12位',
					maxlength:'密码长度应在5-12位'
				},	
				passwordConfirm:{
					required:'请再次输入密码',
					minlength:'密码长度应在5-12位',
					maxlength:'密码长度应在5-12位',
					equalTo:"两次输入的密码不一致" 
				},	
				nickname:{
					required:'请输入昵称',
					minlength:'用户名至少2个字符'
				},	
				become:'选择转正状态',	
				salaryss:{
					number:'请输入数字'
				},	
				salaryzz:{
					number:'请输入数字'
				},	
				housing:{
					number:'请输入数字'
				},	
				traffic:{
					number:'请输入数字'
				},	
				catering:{
					number:'请输入数字'
				},	
				phone:{
					number:'请输入数字'
				},	
				department:'请选择部门',	
				post:'请选择岗位',	
				name:{
					required:'请输入姓名',
					minlength:'用户名至少2个字符'
				},	
				sex:'请选择性别',	
				identity_card:{
					required:'请输入省份证号码',
				},
				mobile:{
					required:'请输入手机号码',
					number:'手机号码格式错误',
					minlength:'11位手机号码',
					maxlength:'11位手机号码'
				},
				education:'请选择学历',
				phone_ugy:{
					number:'手机号码格式错误',
					minlength:'11位手机号码',
					maxlength:'11位手机号码'
				},
				bankaddress:'请选择开户行',
				bank_account:'请输入银行账号'
			},
			errorClass:'cerror',
			
		});
		
});