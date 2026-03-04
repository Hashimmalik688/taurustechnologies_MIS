<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\ZoomToken;
use App\Services\ZoomPhoneApiService;
use Illuminate\Support\Facades\Http;

echo "=== TOKEN → ZOOM ACCOUNT MAPPING ===\n\n";

$tokens = ZoomToken::all();
foreach ($tokens as $t) {
    $service = new ZoomPhoneApiService();
    $token = $service->getAccessTokenForRecord($t);
    if (!$token) {
        $crmUser = User::find($t->user_id);
        echo "Token#{$t->id} CRM_user_id={$t->user_id} (" . ($crmUser ? $crmUser->name : 'DELETED') . ") => FAILED to get token\n";
        continue;
    }

    $resp = Http::timeout(10)->withToken($token)->get('https://api.zoom.us/v2/users/me');
    $crmUser = User::find($t->user_id);
    $crmName = $crmUser ? $crmUser->name : 'DELETED';

    if ($resp->successful()) {
        $data = $resp->json();
        $zoomName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $zoomEmail = $data['email'] ?? '?';
        echo "Token#{$t->id} CRM_user_id={$t->user_id} ({$crmName}) => Zoom: {$zoomName} <{$zoomEmail}>\n";
    } else {
        echo "Token#{$t->id} CRM_user_id={$t->user_id} ({$crmName}) => ERR {$resp->status()}\n";
    }
}

// Now show the 19 Zoom users and which ones are missing tokens
echo "\n=== ZOOM USERS vs CRM MAPPING ===\n";
$zoomUsers = [
    ['name' => 'Brad Anderson', 'email' => 'taurustech12@outlook.com', 'ext' => 843],
    ['name' => 'Brayan Willeford', 'email' => 'taurustech11@outlook.com', 'ext' => 844],
    ['name' => 'Jason Hilton', 'email' => 'taurustech13@outlook.com', 'ext' => 845],
    ['name' => 'Matthew fredericks', 'email' => 'taurus64623@outlook.com', 'ext' => 842],
    ['name' => 'Roman Asher', 'email' => 'Taurus12026@outlook.com', 'ext' => 841],
    ['name' => 'Mark Foster', 'email' => 'rajafak526@gmail.com', 'ext' => 840],
    ['name' => 'Ryan Cooper', 'email' => 'iamryancooper931@gmail.com', 'ext' => 839],
    ['name' => 'Brad Wilson', 'email' => 'bradwilson5552@gmail.com', 'ext' => 838],
    ['name' => 'Steve Henley', 'email' => 'stevehenley02@gmail.com', 'ext' => 837],
    ['name' => 'Bareera Nadeem', 'email' => 'rajputbareera295@gmail.com', 'ext' => 836],
    ['name' => 'Ryan Hamilton', 'email' => 'jordanbrown98001@gmail.com', 'ext' => 835],
    ['name' => 'Sarah Garcia', 'email' => 'syedazara513@gmail.com', 'ext' => 833],
    ['name' => 'Abdullah Ayub', 'email' => 'ayubabdullah536@gmail.com', 'ext' => 828],
    ['name' => 'Maxwell McIntyre', 'email' => 'Maxwellmcintyre357@gmail.com', 'ext' => 826],
    ['name' => 'Farzand Ali', 'email' => 'farzandalimirza2@gmail.com', 'ext' => 824],
    ['name' => 'James Hooper', 'email' => 'ateqrahmaan77@gmail.com', 'ext' => 818],
    ['name' => 'Haris Waqar', 'email' => 'davidhariss37@gmail.com', 'ext' => 805],
    ['name' => 'Roy Matthews', 'email' => 'adeelm100088@gmail.com', 'ext' => 804],
    ['name' => 'Hashim Malik', 'email' => 'business.hashimmalik@gmail.com', 'ext' => 800],
];

foreach ($zoomUsers as $zu) {
    // Match CRM user by email
    $crmUser = User::whereRaw('LOWER(email) = ?', [strtolower($zu['email'])])->first();
    $hasToken = false;
    if ($crmUser) {
        $hasToken = ZoomToken::where('user_id', $crmUser->id)->exists();
    }
    $status = $crmUser
        ? ($hasToken ? 'OK (TOKEN)' : 'NO TOKEN')
        : 'NOT IN CRM';
    echo sprintf("Ext:%-4d %-25s %-40s CRM_id:%-4s %s\n",
        $zu['ext'],
        $zu['name'],
        $zu['email'],
        $crmUser ? $crmUser->id : '-',
        $status
    );
}
