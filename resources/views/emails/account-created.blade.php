@component('mail::message')
# Welcome to {{ $appName }}

Hello {{ $user->name }},

Your account has been successfully created in {{ $appName }}. Here are your login details:

**Email:** {{ $user->email }}  
**Initial Password:** {{ $password }}

Please log in to the system using the link below:

@component('mail::button', ['url' => $loginUrl])
Login to {{ $appName }}
@endcomponent

**Important Security Notes:**
- Change your password immediately after your first login
- Do not share your password with anyone
- Always use HTTPS when accessing the system
- Log out when finished using the system

If you have any issues, please contact your administrator.

Thanks,  
{{ $appName }} Team
@endcomponent
