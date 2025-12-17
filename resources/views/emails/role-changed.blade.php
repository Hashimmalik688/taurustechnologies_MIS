@component('mail::message')
# Role Change Notification

Hello {{ $user->name }},

Your role in {{ $appName }} has been changed.

**Previous Role:** {{ $oldRole }}  
**New Role:** {{ $newRole }}

This change may affect your permissions and what you can access in the system. Please refresh your browser or log in again to see the updated interface.

If you have any questions or if this change was unexpected, please contact your administrator.

Thanks,  
{{ $appName }} Team
@endcomponent
