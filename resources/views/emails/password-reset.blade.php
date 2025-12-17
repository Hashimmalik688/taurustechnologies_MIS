@component('mail::message')
# Password Reset Notification

Hello {{ $user->name }},

Your password has been reset by an administrator in {{ $appName }}.

**Your new password is:**  
{{ $password }}

Please log in using this new password and change it immediately for your security.

@component('mail::button', ['url' => $loginUrl])
Login to {{ $appName }}
@endcomponent

**Important Security Notes:**
- Change your password immediately after logging in
- Do not share your password with anyone
- If you did not request this password reset, please contact your administrator immediately

Thanks,  
{{ $appName }} Team
@endcomponent
