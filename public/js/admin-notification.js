document.addEventListener('DOMContentLoaded',function(){
	const Toast=Swal.mixin({
		toast:true,
		position:'top-end',
		showConfirmButton:false,
		timer:3000,
		timerProgressBar:true,
		didOpen:(toast)=>{
			toast.onmouseenter=Swal.stopTimer;
			toast.onmouseleave=Swal.resumeTimer;
		},
		background:'#ffffff',
		color:'#0f1724',
		iconColor:'#10B981'
	});

	function showSuccess(message){
		Toast.fire({
			icon:'success',
			title:message,
			customClass:{
				popup:'sweet-popup'
			}
		});
	}

	function showError(message){
		Swal.fire({
			icon:'error',
			title:'Error',
			text:message,
			confirmButtonColor:'#10B981',
			background:'#ffffff',
			color:'#0f1724'
		});
	}

	function showConfirm(options){
		return Swal.fire({
			title:options.title||'Are you sure?',
			text:options.text||'',
			icon:options.icon||'warning',
			showCancelButton:true,
			confirmButtonColor:'#10B981',
			cancelButtonColor:'#6b7280',
			confirmButtonText:options.confirmText||'Yes, proceed!',
			cancelButtonText:options.cancelText||'Cancel',
			reverseButtons:true,
			background:'#ffffff',
			color:'#0f1724',
			customClass:{
				popup:'sweet-popup'
			}
		});
	}

	const modal=document.getElementById('sendNotificationModal');
	const openModalBtn=document.getElementById('openSendNotification');
	const closeModalBtn=document.getElementById('closeModal');
	const cancelBtn=document.getElementById('cancelBtn');
	const sendNotificationForm=document.getElementById('sendNotificationForm');

	if(openModalBtn){
		openModalBtn.addEventListener('click',function(){
			modal.classList.add('active');
			document.body.style.overflow='hidden';
		});
	}

	function closeModal(){
		modal.classList.remove('active');
		document.body.style.overflow='';
	}

	if(closeModalBtn){
		closeModalBtn.addEventListener('click',closeModal);
	}

	if(cancelBtn){
		cancelBtn.addEventListener('click',closeModal);
	}

	modal?.addEventListener('click',function(e){
		if(e.target===modal){
			closeModal();
		}
	});

	const recipientTypeSelect=document.getElementById('recipient_type');
	const userIdField=document.getElementById('user_id_field');
	const recipientAddressField=document.getElementById('recipient_address_field');

	if(recipientTypeSelect){
		recipientTypeSelect.addEventListener('change',function(){
			const value=this.value;

			if(value==='user'){
				userIdField.style.display='block';
				recipientAddressField.style.display='none';
			}else if(value==='system_wide'){
				userIdField.style.display='none';
				recipientAddressField.style.display='none';
			}else{
				userIdField.style.display='none';
				recipientAddressField.style.display='block';
			}
		});
		recipientTypeSelect.dispatchEvent(new Event('change'));
	}

	if(sendNotificationForm){
		sendNotificationForm.addEventListener('submit',function(e){
			e.preventDefault();

			const formData=new FormData(this);
			const submitBtn=this.querySelector('button[type="submit"]');
			const originalText=submitBtn.innerHTML;

			submitBtn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Sending...';
			submitBtn.disabled=true;

			fetch(this.action,{
				method:'POST',
				headers:{
					'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
					'Accept':'application/json'
				},
				body:formData
			})
			.then(response=>response.json())
			.then(data=>{
				if(data.success){
					showSuccess(data.message);
					closeModal();
					this.reset();
					recipientTypeSelect.dispatchEvent(new Event('change'));
					setTimeout(()=>{
						window.location.reload();
					},1500);
				}else{
					if(data.errors){
						let errorMessage='Please fix the following errors:\n';
						for(const field in data.errors){
							errorMessage+=`• ${data.errors[field][0]}\n`;
						}
						showError(errorMessage);
					}else{
						showError(data.message||'Failed to send notification');
					}
				}
			})
			.catch(error=>{
				console.error('Error:',error);
				showError('An error occurred. Please try again.');
			})
			.finally(()=>{
				submitBtn.innerHTML=originalText;
				submitBtn.disabled=false;
			});
		});
	}

	const searchInput=document.getElementById('searchInput');
	const userFilter=document.getElementById('userFilter');
	const statusFilter=document.getElementById('statusFilter');
	const applyFilterBtn=document.getElementById('applyFilter');
	const clearBtn=document.getElementById('clearBtn');

	function performSearch(){
		let url=new URL(window.location.origin + '/admin/notifications');
		let params=new URLSearchParams();

		const searchValue=searchInput?.value.trim();
		const userValue=userFilter?.value;
		const statusValue=statusFilter?.value;

		if(searchValue){
			params.set('search',searchValue);
		}

		if(userValue){
			params.set('user_filter',userValue);
		}

		if(statusValue){
			params.set('status_filter',statusValue);
		}

		if(params.toString()){
			url.search=params.toString();
		}

		window.location.href=url.toString();
	}

	if(searchInput){
		searchInput.addEventListener('keypress',function(e){
			if(e.key==='Enter'){
				e.preventDefault();
				performSearch();
			}
		});
	}

	if(applyFilterBtn){
		applyFilterBtn.addEventListener('click',function(e){
			e.preventDefault();
			performSearch();
		});
	}

	if(clearBtn){
		clearBtn.addEventListener('click',function(e){
			e.preventDefault();
			if(searchInput) searchInput.value='';
			if(userFilter) userFilter.value='';
			if(statusFilter) statusFilter.value='';
			window.location.href='/admin/notifications';
		});
	}

	document.querySelectorAll('.notification-card .mark-read').forEach(button=>{
		button.addEventListener('click',function(e){
			e.stopPropagation();
			const notificationId=this.getAttribute('data-id');
			const card=this.closest('.notification-card');

			showConfirm({
				title:'Mark as Read',
				text:'Mark this notification as read?',
				confirmText:'Yes, mark as read'
			}).then(result=>{
				if(result.isConfirmed){
					fetch(`/admin/notifications/mark-read/${notificationId}`,{
						method:'POST',
						headers:{
							'Content-Type':'application/json',
							'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')
						}
					})
					.then(response=>response.json())
					.then(data=>{
						if(data.success){
							card.classList.remove('unread');
							card.classList.add('read');
							card.style.borderLeftColor='#10B981';

							const statusBadge=card.querySelector('.status-badge');
							if(statusBadge){
								statusBadge.classList.remove('status-unread');
								statusBadge.classList.add('status-read');
								statusBadge.textContent='Read';
							}

							this.remove();

							const unreadCountElement=document.querySelector('.stat-card.unread .stat-info h3');
							if(unreadCountElement){
								unreadCountElement.textContent=data.unread_count||'0';
							}

							showSuccess('Notification marked as read');
						}else{
							showError(data.message||'Failed to mark notification as read');
						}
					})
					.catch(error=>{
						console.error('Error:',error);
						showError('An error occurred. Please try again.');
					});
				}
			});
		});
	});

	document.getElementById('markAllReadBtn')?.addEventListener('click',function(){
		showConfirm({
			title:'Mark All as Read',
			text:'Are you sure you want to mark all notifications as read?',
			confirmText:'Yes, mark all!'
		}).then(result=>{
			if(result.isConfirmed){
				fetch('/admin/notifications/mark-all-read',{
					method:'POST',
					headers:{
						'Content-Type':'application/json',
						'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					}
				})
				.then(response=>response.json())
				.then(data=>{
					if(data.success){
						document.querySelectorAll('.notification-card').forEach(card=>{
							card.classList.remove('unread');
							card.classList.add('read');
							card.style.borderLeftColor='#10B981';

							const statusBadge=card.querySelector('.status-badge');
							if(statusBadge){
								statusBadge.classList.remove('status-unread');
								statusBadge.classList.add('status-read');
								statusBadge.textContent='Read';
							}

							const markReadBtn=card.querySelector('.mark-read');
							if(markReadBtn){
								markReadBtn.remove();
							}
						});

						const unreadCountElement=document.querySelector('.stat-card.unread .stat-info h3');
						if(unreadCountElement){
							unreadCountElement.textContent='0';
						}

						showSuccess('All notifications marked as read');
					}else{
						showError(data.message||'Failed to mark all notifications as read');
					}
				})
				.catch(error=>{
					console.error('Error:',error);
					showError('An error occurred. Please try again.');
				});
			}
		});
	});

	document.querySelectorAll('.notification-card').forEach(card=>{
		card.addEventListener('click',function(e){
			if(!e.target.closest('.mark-read') && !e.target.closest('.notification-type')){
				this.style.transform='scale(0.98)';
				setTimeout(()=>{
					this.style.transform='';
				},150);
			}
		});
	});

	document.querySelectorAll('.stat-card').forEach(card=>{
		card.addEventListener('mouseenter',function(){
			const icon=this.querySelector('.stat-icon');
			if(icon){
				icon.style.transform='scale(1.15) rotate(8deg)';
			}
		});

		card.addEventListener('mouseleave',function(){
			const icon=this.querySelector('.stat-icon');
			if(icon){
				icon.style.transform='';
			}
		});
	});

	const style=document.createElement('style');
	style.textContent=`
		.sweet-popup{
			border-radius:12px;
			box-shadow:0 10px 25px rgba(15,23,36,0.15);
		}

		.swal2-icon{
			border-width:3px;
		}

		.swal2-success-ring{
			border-color:#10B981;
		}

		.swal2-success-line-tip,.swal2-success-line-long{
			background-color:#10B981;
		}

		.swal2-x-mark-line-left,.swal2-x-mark-line-right{
			background-color:#ef4444;
		}

		@keyframes fadeOut{
			from{opacity:1;transform:scale(1);}
			to{opacity:0;transform:scale(0.95);}
		}

		@keyframes shimmer{
			0%{background-position:-200% center;}
			100%{background-position:200% center;}
		}

		.loading-shimmer{
			background:linear-gradient(90deg,#f6f8fa 25%,#e5e7eb 50%,#f6f8fa 75%);
			background-size:200% 100%;
			animation:shimmer 1.5s infinite;
		}
	`;
	document.head.appendChild(style);
});
