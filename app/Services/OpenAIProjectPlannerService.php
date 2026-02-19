<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProjectPlannerService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY', ''));
        $this->model = config('services.openai.model', 'gpt-4o');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
    }

    /**
     * Generate a complete project plan from a natural language prompt
     */
    public function generateProjectPlan(string $prompt, array $context = []): array
    {
        $systemPrompt = $this->buildSystemPrompt($context);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 4096,
                'response_format' => ['type' => 'json_object'],
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $plan = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return [
                        'success' => false,
                        'error' => 'Failed to parse AI response as JSON',
                        'raw_response' => $content,
                    ];
                }

                return [
                    'success' => true,
                    'plan' => $plan,
                    'raw_response' => $content,
                    'tokens_used' => $response->json('usage.total_tokens', 0),
                ];
            }

            Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'API request failed: ' . $response->status() . ' - ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI Service Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Build the system prompt with structured output requirements
     */
    private function buildSystemPrompt(array $context = []): string
    {
        $methodology = $context['methodology'] ?? 'agile';
        $teamSize = $context['team_size'] ?? 'not specified';
        $budget = $context['budget'] ?? 'not specified';
        $duration = $context['duration'] ?? 'not specified';

        return <<<PROMPT
You are an expert Project Management AI for Taurus Technologies, a software development company based in Pakistan.
You create comprehensive, actionable project plans based on descriptions provided.

Context:
- Methodology preference: {$methodology}
- Team size: {$teamSize}
- Budget: {$budget}
- Expected duration: {$duration}

Generate a complete project plan in the following JSON structure:

{
  "project_summary": {
    "name": "Project name",
    "description": "Brief description",
    "methodology": "agile|waterfall|hybrid|kanban",
    "estimated_duration_weeks": number,
    "estimated_budget_usd": number,
    "tech_stack": "Suggested technology stack",
    "category": "Web App|Mobile|API|Desktop|Data|AI/ML|Other"
  },
  "milestones": [
    {
      "name": "Milestone name",
      "description": "What this milestone delivers",
      "week_number": number,
      "deliverables": ["list of deliverables"]
    }
  ],
  "wbs": [
    {
      "code": "1",
      "name": "Phase name",
      "level": "phase",
      "children": [
        {
          "code": "1.1",
          "name": "Deliverable name",
          "level": "deliverable",
          "estimated_hours": number,
          "children": [
            {
              "code": "1.1.1",
              "name": "Work package",
              "level": "work_package",
              "estimated_hours": number
            }
          ]
        }
      ]
    }
  ],
  "tasks": [
    {
      "name": "Task name",
      "description": "Task description",
      "priority": "low|medium|high|urgent",
      "milestone_index": number,
      "estimated_hours": number,
      "story_points": number,
      "dependencies": [task_index_numbers],
      "skills_required": ["skill1", "skill2"]
    }
  ],
  "sprints": [
    {
      "name": "Sprint name",
      "goal": "Sprint goal",
      "duration_weeks": number,
      "task_indices": [task_index_numbers]
    }
  ],
  "risks": [
    {
      "title": "Risk title",
      "description": "Risk description",
      "probability": "very_low|low|medium|high|very_high",
      "impact": "very_low|low|medium|high|very_high",
      "category": "technical|schedule|budget|resource|scope|quality|external",
      "mitigation_plan": "How to mitigate"
    }
  ],
  "team_roles": [
    {
      "role": "Role title",
      "raci_role": "responsible|accountable|consulted|informed",
      "responsibilities": "Key responsibilities",
      "count": number
    }
  ],
  "kpis": [
    {
      "name": "KPI name",
      "target": "Target value",
      "measurement": "How to measure"
    }
  ]
}

Rules:
1. Be realistic with time estimates for a Pakistani software team
2. Include proper dependency chains between tasks
3. Include at least 3-5 identified risks with mitigation plans
4. Create sprints only for agile/hybrid methodologies
5. WBS should have 3 levels minimum (Phase > Deliverable > Work Package)
6. Milestones should be evenly spaced across the project timeline
7. Story points should follow Fibonacci sequence (1, 2, 3, 5, 8, 13, 21)
8. Include both technical and non-technical tasks (meetings, documentation, QA)
PROMPT;
    }

    /**
     * Check if the API key is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your-api-key-here';
    }
}
