<x-app-layout>
    <style>
        .dashboard-top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.75rem; }
        .search-box { display: flex; align-items: center; gap: 0.65rem; background: rgba(255, 250, 245, 0.95); border-radius: 16px; padding: 0.9rem 1.1rem; border: 1px solid #f2c8a5; box-shadow: 0 10px 28px rgba(150, 73, 16, 0.08); width: 320px; }
        .search-box input { border: none; outline: none; flex-grow: 1; font-size: 0.92rem; color: #33231a; background: transparent; }
        .user-profile { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #1d1d1d 0%, #474747 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; font-weight: 700; box-shadow: 0 10px 24px rgba(0, 0, 0, 0.14); cursor: pointer; border: 3px solid #ffd5b4; }
        .welcome-box {
            background:
                radial-gradient(circle at top right, rgba(255, 209, 170, 0.26), transparent 25%),
                linear-gradient(135deg, #fff8f1 0%, #fff0df 100%);
            border-radius: 28px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 18px 40px rgba(156, 79, 21, 0.08);
            border: 1px solid #f3c8a4;
            position: relative;
            overflow: hidden;
        }
        .welcome-box::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            right: -90px;
            bottom: -110px;
            background: radial-gradient(circle, rgba(243, 106, 16, 0.22), transparent 68%);
        }
        .welcome-brand { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem; position: relative; z-index: 1; }
        .welcome-brand-logo { width: 66px; height: 66px; border-radius: 50%; background: #fff7ed; padding: 0.4rem; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08); }
        .welcome-brand-copy { display: flex; flex-direction: column; gap: 0.25rem; }
        .welcome-kicker { font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.14em; color: #9a4a12; }
        .welcome-title { font-size: 1.95rem; font-weight: 800; color: #191919; margin-bottom: 0.35rem; position: relative; z-index: 1; }
        .welcome-subtitle { font-size: 0.95rem; color: #734c33; position: relative; z-index: 1; max-width: 720px; }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-box { background: rgba(255, 250, 245, 0.96); border-radius: 20px; padding: 1.5rem; box-shadow: 0 10px 24px rgba(145, 71, 18, 0.08); text-align: center; border: 1px solid #f3c8a4; }
        .stat-box-value { font-size: 2.5rem; font-weight: 800; color: #f36a10; line-height: 1; margin-bottom: 0.5rem; }
        .stat-box-label { font-size: 0.76rem; font-weight: 700; color: #88573c; text-transform: uppercase; letter-spacing: 0.08em; }
        .section-header { font-size: 1rem; font-weight: 800; color: #1d1d1d; margin-bottom: 1.2rem; margin-top: 0; text-transform: uppercase; letter-spacing: 0.08em; }
        .action-cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .action-card { border-radius: 22px; padding: 2rem; color: white; position: relative; overflow: hidden; transition: all 0.3s ease; text-decoration: none; display: flex; flex-direction: column; min-height: 220px; box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12); }
        .action-card:hover { transform: translateY(-8px); box-shadow: 0 20px 36px rgba(0, 0, 0, 0.18); }
        .action-card::before {
            content: "";
            position: absolute;
            inset: auto -40px -40px auto;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        .action-card.coral { background: linear-gradient(145deg, #ff8f2f 0%, #f36a10 55%, #ba3d00 100%); }
        .action-card.cyan { background: linear-gradient(145deg, #2c2c2c 0%, #111111 100%); }
        .card-icon { margin-bottom: 1rem; display: inline-flex; }
        .card-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem; }
        .card-subtitle { font-size: 0.8rem; opacity: 0.9; margin-bottom: 1rem; }
        .card-footer { margin-top: auto; display: flex; justify-content: space-between; align-items: flex-end; padding-top: 1rem; }
        .card-stats { font-size: 0.875rem; opacity: 0.95; }
        .card-stats strong { display: block; font-size: 1.5rem; font-weight: 800; line-height: 1; }
        .card-arrow { font-size: 1.5rem; transition: transform 0.3s ease; }
        .action-card:hover .card-arrow { transform: translateX(4px); }
        .cta-card { background: linear-gradient(135deg, #fffaf5 0%, #fff2e4 100%); border-radius: 24px; padding: 2rem; box-shadow: 0 16px 32px rgba(145, 71, 18, 0.08); display: flex; justify-content: space-between; align-items: center; gap: 2rem; border: 1px solid #f3c8a4; }
        .cta-content h3 { font-size: 1.25rem; font-weight: 800; color: #1d1d1d; margin-bottom: 0.5rem; }
        .cta-content p { font-size: 0.92rem; color: #7b5a45; }
        .cta-button { background: linear-gradient(135deg, #f36a10 0%, #bf4300 100%); color: white; border: none; border-radius: 999px; padding: 0.85rem 1.6rem; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.3s ease; white-space: nowrap; text-decoration: none; display: inline-block; box-shadow: 0 12px 24px rgba(191, 67, 0, 0.22); }
        .cta-button:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
        @media (max-width: 768px) {
            .dashboard-top { flex-direction: column; align-items: stretch; }
            .search-box { width: 100%; }
            .action-cards-grid { grid-template-columns: 1fr; }
            .cta-card { flex-direction: column; text-align: center; }
            .welcome-brand { align-items: flex-start; }
        }
    </style>

    <div id="spa-page">
        <div class="dashboard-top">
            <div class="search-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="11" cy="11" r="6" stroke="#666" stroke-width="1.8"/>
                    <path d="M20 20L16.65 16.65" stroke="#666" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <input type="text" placeholder="Search students...">
            </div>
            <div class="user-profile">{{ substr(auth()->user()->name, 0, 1) }}</div>
        </div>

    <div class="welcome-box">
        <div class="welcome-brand">
            <div class="welcome-brand-logo">
                <x-application-logo class="block h-full w-full" />
            </div>
            <div class="welcome-brand-copy">
                <span class="welcome-kicker">College of Computing Studies</span>
                <h1 class="welcome-title">Welcome back, {{ auth()->user()->name }}!</h1>
            </div>
        </div>
        <p class="welcome-subtitle">Monitor student records, review activity, and manage the College of Computing Studies dashboard with the updated CCS identity.</p>
    </div>

    <h2 class="section-header">Statistics</h2>
    <div class="stat-grid">
        <div class="stat-box">
            <div class="stat-box-value">{{ App\Models\Student::count() }}</div>
            <div class="stat-box-label">Total Students</div>
        </div>
        <div class="stat-box">
            <div class="stat-box-value">{{ App\Models\StudentSkill::distinct('student_id')->count() }}</div>
            <div class="stat-box-label">With Skills</div>
        </div>
        <div class="stat-box">
            <div class="stat-box-value">{{ App\Models\StudentActivity::distinct('student_id')->count() }}</div>
            <div class="stat-box-label">With Activities</div>
        </div>
    </div>

    <h2 class="section-header">Quick Access</h2>
    <div class="action-cards-grid">
        <a href="{{ route('students.index') }}" class="action-card coral">
            <div class="card-icon">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M4 19C4.8 16.6 6.7 15 9 15C11.3 15 13.2 16.6 14 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="17" cy="9" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M15.5 19C16 17.3 17.3 16.1 19 15.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="card-title">Students</div>
            <div class="card-subtitle">Manage student profiles</div>
            <div class="card-footer">
                <div class="card-stats">
                    <strong>{{ App\Models\Student::count() }}</strong>
                    <div>Total</div>
                </div>
                <div class="card-arrow">&rarr;</div>
            </div>
        </a>

        <a href="{{ route('queries.index') }}" class="action-card cyan">
            <div class="card-icon">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M20 20L16.65 16.65" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="card-title">Search System</div>
            <div class="card-subtitle">Find student profiles quickly</div>
            <div class="card-footer">
                <div class="card-stats">
                    <strong>Advanced</strong>
                    <div>Search</div>
                </div>
                <div class="card-arrow">&rarr;</div>
            </div>
        </a>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.account-settings') }}" class="action-card coral">
                <div class="card-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M6 19C6.9 16.4 9 15 12 15C15 15 17.1 16.4 18 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M18.5 5.5L20 7L16.5 10.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="card-title">Admin Account</div>
                <div class="card-subtitle">Change system admin email and password</div>
                <div class="card-footer">
                    <div class="card-stats">
                        <strong>Secure</strong>
                        <div>Settings</div>
                    </div>
                    <div class="card-arrow">&rarr;</div>
                </div>
            </a>

            <a href="{{ route('audit-logs.index') }}" class="action-card cyan">
                <div class="card-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 4.5H17C18.1 4.5 19 5.4 19 6.5V19.5H7C5.9 19.5 5 18.6 5 17.5V6.5C5 5.4 5.9 4.5 7 4.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M8.5 8H15.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M8.5 11.5H15.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M8.5 15H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="card-title">Audit Logs</div>
                <div class="card-subtitle">Review recorded admin changes</div>
                <div class="card-footer">
                    <div class="card-stats">
                        <strong>Admin</strong>
                        <div>Trail</div>
                    </div>
                    <div class="card-arrow">&rarr;</div>
                </div>
            </a>

            <a href="{{ route('submissions.index') }}" class="action-card coral">
                <div class="card-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 6.5C5 5.4 5.9 4.5 7 4.5H17C18.1 4.5 19 5.4 19 6.5V17.5C19 18.6 18.1 19.5 17 19.5H7C5.9 19.5 5 18.6 5 17.5V6.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M8 8H16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M8 11.5H13.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M15.5 15.5L17 17L20 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="card-title">Review Queue</div>
                <div class="card-subtitle">Approve student skills and activities</div>
                <div class="card-footer">
                    <div class="card-stats">
                        <strong>Pending</strong>
                        <div>Submissions</div>
                    </div>
                    <div class="card-arrow">&rarr;</div>
                </div>
            </a>
        @endif
    </div>

    <div class="cta-card">
        <div class="cta-content">
            <h3>Ready to add a new CCS student?</h3>
            <p>Create a new student profile and start tracking their information under the College of Computing Studies.</p>
        </div>
        <a href="{{ route('students.create') }}" class="cta-button">+ Add New Student</a>
    </div>
    </div>
</x-app-layout>
