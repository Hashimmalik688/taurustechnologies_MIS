@extends('layouts.master')

@section('title') Create New Project @endsection

@section('css')
<style>
    .create-header { background: linear-gradient(135deg, var(--bs-gradient-start) 0%, var(--bs-gradient-end) 100%); border-radius: 20px; padding: 35px; margin-bottom: 30px; color: var(--bs-white, #fff); }
    .form-section { background: var(--bs-white, #fff); border-radius: 16px; padding: 30px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid var(--bs-surface-200); }
    .form-section h5 { color: var(--bs-surface-900); font-weight: 700; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid var(--bs-print-bg-alt); }
    .form-section h5 i { color: var(--bs-gradient-start); margin-right: 8px; }
    .ai-planner-section { background: linear-gradient(135deg, var(--bs-surface-900) 0%, var(--bs-surface-800) 100%); border-radius: 16px; padding: 30px; color: var(--bs-white, #fff); margin-bottom: 20px; }
    .ai-planner-section h5 { color: var(--bs-ui-purple); border-bottom-color: rgba(255,255,255,0.1); }
    .ai-planner-section .form-control, .ai-planner-section .form-select { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: var(--bs-white, #fff); }
    .ai-planner-section .form-control::placeholder { color: rgba(255,255,255,0.5); }
    .ai-planner-section .form-control:focus, .ai-planner-section .form-select:focus { background: rgba(255,255,255,0.15); border-color: var(--bs-ui-purple); color: var(--bs-white, #fff); box-shadow: 0 0 0 0.25rem rgba(167,139,250,0.25); }
    .ai-planner-section .form-label { color: var(--bs-surface-400); }
    .ai-result-box { background: rgba(0,0,0,0.3); border-radius: 12px; padding: 20px; margin-top: 20px; display: none; max-height: 400px; overflow-y: auto; }
    .ai-result-box pre { color: var(--bs-ui-purple); font-size: 0.85rem; white-space: pre-wrap; }
    .btn-ai { background: linear-gradient(135deg, var(--bs-ui-purple), var(--bs-ui-indigo)); color: var(--bs-white, #fff); border: none; padding: 12px 30px; border-radius: 12px; font-weight: 600; }
    .btn-ai:hover { background: linear-gradient(135deg, var(--bs-ui-purple), var(--bs-ui-indigo)); color: var(--bs-white, #fff); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(139,92,246,0.4); }
    .milestone-row { background: var(--bs-surface-bg-light); border-radius: 10px; padding: 15px; margin-bottom: 10px; }
    .spinner-ai { display: none; }
    .spinner-ai.active { display: inline-block; }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') <a href="{{ route('epms.index') }}">EPMS</a> @endslot
        @slot('title') Create Project @endslot
    @endcomponent

    <div class="create-header">
        <h2><i class="bx bx-rocket me-2"></i>Create New Project</h2>
        <p class="mb-0">Set up a new internal project for Taurus Technologies</p>
    </div>

    <!-- AI Project Planner Section -->
    <div class="ai-planner-section" id="ai-planner">
        <h5><i class="bx bx-brain"></i> AI Project Planner</h5>
        <p class="text-muted small">Describe your project idea and let AI generate a complete plan with tasks, milestones, risks, and more.</p>

        @if($aiConfigured)
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Describe Your Project *</label>
                    <textarea class="form-control" id="ai_prompt" rows="4" placeholder="e.g., Build a React Native mobile app for inventory management with barcode scanning, real-time sync, and analytics dashboard. Team of 5 developers, 3-month timeline..."></textarea>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Methodology</label>
                    <select class="form-select" id="ai_methodology">
                        <option value="agile">Agile</option>
                        <option value="waterfall">Waterfall</option>
                        <option value="hybrid">Hybrid</option>
                        <option value="kanban">Kanban</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Team Size</label>
                    <input type="number" class="form-control" id="ai_team_size" placeholder="5" min="1">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Budget (USD)</label>
                    <input type="number" class="form-control" id="ai_budget" placeholder="50000">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Duration</label>
                    <input type="text" class="form-control" id="ai_duration" placeholder="3 months">
                </div>
            </div>
            <button type="button" class="btn btn-ai" id="generateAiPlan">
                <span class="spinner-border spinner-border-sm spinner-ai me-2" id="aiSpinner"></span>
                <i class="bx bx-magic-wand me-1"></i> Generate AI Plan
            </button>

            <div class="ai-result-box" id="aiResultBox">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-success mb-0"><i class="bx bx-check-circle me-1"></i> Plan Generated!</h6>
                    <button type="button" class="btn btn-sm btn-outline-success d-none" id="applyAiPlan">
                        <i class="bx bx-import me-1"></i> Apply to Form
                    </button>
                </div>
                <pre id="aiResultContent"></pre>
            </div>
        @else
            <div class="alert alert-warning bg-transparent border border-warning">
                <i class="bx bx-info-circle me-2"></i>
                <strong>API Key Required:</strong> Add <code>OPENAI_API_KEY</code> to your <code>.env</code> file to enable AI project planning.
            </div>
        @endif
    </div>

    <!-- Manual Project Form -->
    <form action="{{ route('epms.store') }}" method="POST" id="projectForm">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <!-- Project Information -->
                <div class="form-section">
                    <h5><i class="bx bx-briefcase-alt"></i> Project Information</h5>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Project Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" name="category" id="category">
                                <option value="">Select Category</option>
                                <option value="Web Application" {{ old('category') == 'Web Application' ? 'selected' : '' }}>Web Application</option>
                                <option value="Mobile App" {{ old('category') == 'Mobile App' ? 'selected' : '' }}>Mobile App</option>
                                <option value="API/Backend" {{ old('category') == 'API/Backend' ? 'selected' : '' }}>API/Backend</option>
                                <option value="Desktop App" {{ old('category') == 'Desktop App' ? 'selected' : '' }}>Desktop App</option>
                                <option value="Data/Analytics" {{ old('category') == 'Data/Analytics' ? 'selected' : '' }}>Data/Analytics</option>
                                <option value="AI/ML" {{ old('category') == 'AI/ML' ? 'selected' : '' }}>AI/ML</option>
                                <option value="DevOps" {{ old('category') == 'DevOps' ? 'selected' : '' }}>DevOps</option>
                                <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="objectives" class="form-label">Objectives</label>
                            <textarea class="form-control" id="objectives" name="objectives" rows="2" placeholder="Key objectives and deliverables...">{{ old('objectives') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tech_stack" class="form-label">Tech Stack</label>
                            <input type="text" class="form-control" id="tech_stack" name="tech_stack" value="{{ old('tech_stack') }}" placeholder="e.g., Laravel, React, PostgreSQL">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="repository_url" class="form-label">Repository URL</label>
                            <input type="url" class="form-control" id="repository_url" name="repository_url" value="{{ old('repository_url') }}" placeholder="https://github.com/...">
                        </div>
                    </div>
                </div>

                <!-- Milestones -->
                <div class="form-section">
                    <h5><i class="bx bx-flag"></i> Milestones</h5>
                    <div id="milestonesContainer">
                        <div class="milestone-row">
                            <div class="row">
                                <div class="col-md-5"><input type="text" class="form-control" name="milestones[0][name]" placeholder="Milestone name"></div>
                                <div class="col-md-4"><input type="date" class="form-control" name="milestones[0][due_date]"></div>
                                <div class="col-md-3"><input type="text" class="form-control" name="milestones[0][description]" placeholder="Description"></div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addMilestone">
                        <i class="bx bx-plus"></i> Add Milestone
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Project Settings -->
                <div class="form-section">
                    <h5><i class="bx bx-cog"></i> Settings</h5>
                    <div class="mb-3">
                        <label class="form-label">Methodology *</label>
                        <select class="form-select" name="methodology" required>
                            <option value="agile" {{ old('methodology') == 'agile' ? 'selected' : '' }}>Agile (Scrum)</option>
                            <option value="kanban" {{ old('methodology') == 'kanban' ? 'selected' : '' }}>Kanban</option>
                            <option value="waterfall" {{ old('methodology') == 'waterfall' ? 'selected' : '' }}>Waterfall</option>
                            <option value="hybrid" {{ old('methodology') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority *</label>
                        <select class="form-select" name="priority" required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" class="form-control" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Deadline *</label>
                            <input type="date" class="form-control" name="deadline" value="{{ old('deadline') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Currency *</label>
                            <select class="form-select" name="currency" required>
                                <option value="PKR" {{ old('currency', 'PKR') == 'PKR' ? 'selected' : '' }}>PKR</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Budget</label>
                            <input type="number" class="form-control" name="budget" value="{{ old('budget') }}" placeholder="0" step="0.01">
                        </div>
                    </div>
                </div>

                <!-- Team -->
                <div class="form-section">
                    <h5><i class="bx bx-group"></i> Team</h5>
                    <div class="mb-3">
                        <label class="form-label">Project Manager</label>
                        <select class="form-select" name="project_manager_id">
                            <option value="">Select PM</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('project_manager_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team Members</label>
                        <select class="form-select" name="team_members[]" multiple size="5">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl to select multiple</small>
                    </div>
                </div>

 <button type="submit" class="btn btn-success w-100 py-3 u-rounded-12 u-fw-600 u-fs-110">
                    <i class="bx bx-check-circle me-1"></i> Create Project
                </button>
            </div>
        </div>
    </form>
@endsection

@section('script')
<script>
    // Add Milestone rows
    let msIndex = 1;
    document.getElementById('addMilestone').addEventListener('click', function() {
        const container = document.getElementById('milestonesContainer');
        const row = document.createElement('div');
        row.className = 'milestone-row';
        row.innerHTML = `
            <div class="row">
                <div class="col-md-5"><input type="text" class="form-control" name="milestones[${msIndex}][name]" placeholder="Milestone name"></div>
                <div class="col-md-4"><input type="date" class="form-control" name="milestones[${msIndex}][due_date]"></div>
                <div class="col-md-2"><input type="text" class="form-control" name="milestones[${msIndex}][description]" placeholder="Description"></div>
                <div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.milestone-row').remove()"><i class="bx bx-trash"></i></button></div>
            </div>`;
        container.appendChild(row);
        msIndex++;
    });

    // AI Plan Generation
    let generatedPlan = null;

    document.getElementById('generateAiPlan')?.addEventListener('click', function() {
        const prompt = document.getElementById('ai_prompt').value;
        if (!prompt || prompt.length < 20) {
            alert('Please describe your project in at least 20 characters.');
            return;
        }

        const btn = this;
        const spinner = document.getElementById('aiSpinner');
        btn.disabled = true;
        spinner.classList.add('active');

        fetch('{{ route("epms.ai.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                prompt: prompt,
                methodology: document.getElementById('ai_methodology').value,
                team_size: document.getElementById('ai_team_size').value || null,
                budget: document.getElementById('ai_budget').value || null,
                duration: document.getElementById('ai_duration').value || null
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            spinner.classList.remove('active');

            const resultBox = document.getElementById('aiResultBox');
            const resultContent = document.getElementById('aiResultContent');

            if (data.success) {
                generatedPlan = data.plan;
                resultContent.textContent = JSON.stringify(data.plan, null, 2);
                resultBox.style.display = 'block';
                document.getElementById('applyAiPlan').style.display = 'inline-block';
            } else {
                resultContent.textContent = 'Error: ' + (data.error || 'Unknown error');
                resultBox.style.display = 'block';
            }
        })
        .catch(err => {
            btn.disabled = false;
            spinner.classList.remove('active');
            alert('Failed to generate plan: ' + err.message);
        });
    });

    // Apply AI plan to form
    document.getElementById('applyAiPlan')?.addEventListener('click', function() {
        if (!generatedPlan) return;

        const summary = generatedPlan.project_summary || {};
        if (summary.name) document.getElementById('name').value = summary.name;
        if (summary.description) document.getElementById('description').value = summary.description;
        if (summary.tech_stack) document.getElementById('tech_stack').value = summary.tech_stack;
        if (summary.category) {
            const catSelect = document.getElementById('category');
            for (let opt of catSelect.options) {
                if (opt.value.toLowerCase().includes(summary.category.toLowerCase())) {
                    opt.selected = true;
                    break;
                }
            }
        }

        // Set methodology
        if (summary.methodology) {
            document.querySelector(`select[name="methodology"]`).value = summary.methodology;
        }

        // Set budget
        if (summary.estimated_budget_usd) {
            document.querySelector(`input[name="budget"]`).value = summary.estimated_budget_usd;
        }

        // Set deadline based on duration
        if (summary.estimated_duration_weeks) {
            const startDate = new Date(document.querySelector('input[name="start_date"]').value);
            const endDate = new Date(startDate);
            endDate.setDate(endDate.getDate() + (summary.estimated_duration_weeks * 7));
            document.querySelector('input[name="deadline"]').value = endDate.toISOString().split('T')[0];
        }

        // Add milestones
        if (generatedPlan.milestones && generatedPlan.milestones.length > 0) {
            const container = document.getElementById('milestonesContainer');
            container.innerHTML = '';
            generatedPlan.milestones.forEach((ms, idx) => {
                const startDate = new Date(document.querySelector('input[name="start_date"]').value);
                const msDate = new Date(startDate);
                msDate.setDate(msDate.getDate() + ((ms.week_number || (idx + 1) * 2) * 7));

                const row = document.createElement('div');
                row.className = 'milestone-row';
                row.innerHTML = `
                    <div class="row">
                        <div class="col-md-5"><input type="text" class="form-control" name="milestones[${idx}][name]" value="${ms.name}"></div>
                        <div class="col-md-4"><input type="date" class="form-control" name="milestones[${idx}][due_date]" value="${msDate.toISOString().split('T')[0]}"></div>
                        <div class="col-md-3"><input type="text" class="form-control" name="milestones[${idx}][description]" value="${ms.description || ''}"></div>
                    </div>`;
                container.appendChild(row);
            });
            msIndex = generatedPlan.milestones.length;
        }

        alert('AI plan applied to form! Review and adjust before submitting. Tasks, risks, WBS, and sprints will be created after project creation using "Apply AI Plan" on the project dashboard.');
    });
</script>
@endsection
