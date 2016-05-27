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
				education1:'required',
				phone_ugy:{
					number:true,
					minlength:11,
					maxlength:11
				},
			},
			messages:{				
				username:{
					required:'请输入用户名',
					minlength:'用户名至少2个字符'
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
				education1:'请选择学历',
				phone_ugy:{
					number:'手机号码格式错误',
					minlength:'11位手机号码',
					maxlength:'11位手机号码'
				}
			},
			errorClass:'cerror',
			
		});
		
});
