<x-app-layout>
    <style>
        .search-card { background: white; border-radius: 12px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); }
        .search-title { font-size: 1.25rem; font-weight: 700; color: #333; margin-bottom: 1.5rem; }
        .quick-search-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .search-item { padding: 1.5rem; border-radius: 12px; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 2px solid #e9ecef; text-decoration: none; transition: all 0.3s ease; display: block; }
        .search-item:hover { border-color: #5b5bee; background: linear-gradient(135deg, #f0f0ff 0%, #ffffff 100%); box-shadow: 0 4px 12px rgba(91, 91, 238, 0.1); }
        .search-item-title { font-size: 0.95rem; font-weight: 700; color: #333; margin-bottom: 0.5rem; }
        .search-item-desc { font-size: 0.75rem; color: #999; }
        .search-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem; }
        .form-group-search { display: flex; flex-direction: column; }
        .form-label-search { font-size: 0.875rem; font-weight: 600; color: #333; margin-bottom: 0.5rem; }
        .form-input-search, .form-select-search { padding: 0.75rem 1rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; transition: all 0.3s ease; }
        .form-input-search:focus, .form-select-search:focus { outline: none; border-color: #5b5bee; box-shadow: 0 0 0 3px rgba(91, 91, 238, 0.1); }
        .search-controls { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .view-options { display: flex; gap: 1rem; }
        .radio-option { display: flex; align-items: center; gap: 0.5rem; }
        .radio-option input { cursor: pointer; }
        .radio-option label { cursor: pointer; font-size: 0.875rem; color: #333; }
        .btn-search { background: linear-gradient(135deg, #5b5bee 0%, #b85dd5 100%); color: white; border: none; border-radius: 8px; padding: 0.75rem 2rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.3s ease; }
        .btn-search:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
        .info-box { background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%); border: 2px solid #0ea5e9; border-radius: 12px; padding: 1.5rem; }
        .info-title { font-size: 1rem; font-weight: 700; color: #0369a1; margin-bottom: 1rem; }
        .info-list { list-style: none; padding: 0; font-size: 0.875rem; color: #0369a1; }
        .info-list li { margin-bottom: 0.5rem; }
        @media (max-width: 768px) {
            .search-form { grid-template-columns: 1fr; }
            .search-controls { flex-direction: column; align-items: flex-start; }
            .view-options { flex-direction: column; width: 100%; }
            .btn-search { width: 100%; }
        }
    </style>

    <div class="search-card" style="margin-bottom: 2.5rem;">
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #333; margin: 0 0 0.25rem 0;">Student Search & Filtering</h1>
        <p style="font-size: 0.875rem; color: #999; margin: 0;">Search and filter students by skills, activities, and other criteria</p>
    </div>

    <div class="search-card">
        <h3 class="search-title">Quick Search</h3>
        <div class="quick-search-grid">
            <a href="{{ route('queries.skill', ['skill' => 'Programming']) }}" class="search-item">
                <div class="search-item-title">Programming Skills</div>
                <div class="search-item-desc">Find students with programming knowledge</div>
            </a>
            <a href="{{ route('queries.skill', ['skill' => 'Basketball']) }}" class="search-item">
                <div class="search-item-title">Basketball Skills</div>
                <div class="search-item-desc">Find students with basketball experience</div>
            </a>
            <a href="{{ route('queries.activity', ['activity' => 'Programming Club']) }}" class="search-item">
                <div class="search-item-title">Programming Club</div>
                <div class="search-item-desc">Find club members</div>
            </a>
            <a href="{{ route('queries.activity', ['activity' => 'Basketball Team']) }}" class="search-item">
                <div class="search-item-title">Basketball Team</div>
                <div class="search-item-desc">Find team members</div>
            </a>
        </div>
    </div>

    <div class="search-card">
        <h3 class="search-title">Advanced Search</h3>
        <form method="GET" action="{{ route('queries.advanced') }}">
            <div class="search-form">
                <div class="form-group-search">
                    <label class="form-label-search" for="skill">Skill</label>
                    <input id="skill" class="form-input-search" type="text" name="skill" value="{{ old('skill') }}" placeholder="e.g., Programming" />
                </div>
                <div class="form-group-search">
                    <label class="form-label-search" for="activity">Activity</label>
                    <input id="activity" class="form-input-search" type="text" name="activity" value="{{ old('activity') }}" placeholder="e.g., Programming Club" />
                </div>
                <div class="form-group-search">
                    <label class="form-label-search" for="year_level">Year Level</label>
                    <select id="year_level" name="year_level" class="form-select-search">
                        <option value="">All Years</option>
                        <option value="1" {{ old('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                        <option value="2" {{ old('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3" {{ old('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4" {{ old('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                    </select>
                </div>
                <div class="form-group-search">
                    <label class="form-label-search" for="status">Status</label>
                    <select id="status" name="status" class="form-select-search">
                        <option value="">All Statuses</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="graduated" {{ old('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
            </div>

            <div class="search-controls">
                <div class="view-options">
                    <div class="radio-option">
                        <input type="radio" id="format-table" name="format" value="table" {{ old('format', 'table') == 'table' ? 'checked' : '' }} />
                        <label for="format-table">Table View</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="format-cards" name="format" value="cards" {{ old('format') == 'cards' ? 'checked' : '' }} />
                        <label for="format-cards">Card View</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="format-list" name="format" value="list" {{ old('format') == 'list' ? 'checked' : '' }} />
                        <label for="format-list">List View</label>
                    </div>
                </div>

                <button type="submit" class="btn-search">Search Students</button>
            </div>
        </form>
    </div>

    <div class="info-box">
        <h4 class="info-title">Search Tips</h4>
        <ul class="info-list">
            <li>&check; Use partial skill names (e.g., "Program" will find "Programming")</li>
            <li>&check; Combine multiple criteria for more specific results</li>
            <li>&check; Try different display formats for various views</li>
            <li>&check; All searches support pagination for large result sets</li>
        </ul>
    </div>
</x-app-layout>
