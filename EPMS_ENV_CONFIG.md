# EPMS Configuration Guide

## Required .env Variables

Add these to your `.env` file to enable all EPMS features:

```env
# OpenAI API Configuration (for AI Project Planner)
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_MODEL=gpt-4o

# Optional: Adjust if using different models
# OPENAI_MODEL=gpt-4-turbo
# OPENAI_MODEL=gpt-3.5-turbo
```

## How to Get OpenAI API Key

1. Go to https://platform.openai.com/
2. Sign up or log in
3. Navigate to **API Keys** section
4. Click **Create new secret key**
5. Copy the key (starts with `sk-proj-` or `sk-`)
6. Paste into `.env` file

⚠️ **Important**: Never commit `.env` file to git! OpenAI keys are sensitive credentials.

## Testing AI Configuration

Run this in terminal to verify:

```bash
php artisan tinker
```

Then enter:
```php
$service = new \App\Services\OpenAIProjectPlannerService();
$service->isConfigured(); // Should return true
```

## Cost Considerations

- **GPT-4o**: ~$5-15 per 1M tokens
- **Average Project Plan**: ~2,000-5,000 tokens (input + output)
- **Estimated Cost per Plan**: $0.01 - $0.08

Set usage limits in OpenAI dashboard to control costs.

## Fallback (No OpenAI)

If you don't configure OpenAI:
- AI Planner buttons will be hidden in UI
- Manual project creation still fully functional
- All other EPMS features work normally

## Verification Checklist

- ✅ `.env` file has `OPENAI_API_KEY=sk-...`
- ✅ `.env` file has `OPENAI_MODEL=gpt-4o`
- ✅ `config/services.php` has openai array (already added)
- ✅ Run `php artisan config:cache` after changes
- ✅ Check EPMS create page for "AI Planner" section
