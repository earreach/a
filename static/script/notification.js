/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
function Dnotification(id, url, icon, title, content) {
	if(window.location.href.indexOf('/message'+DTExt) != -1 || window.location.href.indexOf('/im'+DTExt) != -1) return;
	if(get_local('notification_'+id) == title) return;
	set_local('notification_'+id, title);
	if(('Notification' in window)) {
		if(Notification.permission === 'granted') {
			const notification = new Notification(title, {
				body:content,
				icon:icon
			});
			notification.onclick = function(event) {
				event.preventDefault();
				window.focus();window.top.location = url;notification.close();
			}
		} else if(Notification.permission !== 'denied') {
			Notification.requestPermission().then((permission) => {
				if (permission === 'granted') {
					const notification = new Notification(title, {
						body:content,
						icon:icon
					});
					notification.onclick = function(event) {
						event.preventDefault();
						window.focus();window.top.location = url;notification.close();
					}
				}
			});
		}
	}
}