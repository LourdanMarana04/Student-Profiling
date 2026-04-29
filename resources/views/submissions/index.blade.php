<x-app-layout>
    <style>
        .page-shell { display: grid; gap: 1.5rem; }
        .page-card { background: white; border-radius: 18px; padding: 1.6rem 1.9rem; box-shadow: 0 16px 38px rgba(150, 73, 16, 0.08); border: 1px solid #f2d1b6; }
        .page-title { font-size: 1.8rem; font-weight: 800; color: #23150d; margin: 0 0 0.35rem; }
        .page-copy { margin: 0; color: #80553c; font-size: 0.95rem; }
        .queue-grid { display: grid; gap: 1.5rem; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .queue-card { background: white; border-radius: 18px; box-shadow: 0 16px 38px rgba(150, 73, 16, 0.08); border: 1px solid #f2d1b6; overflow: hidden; }
        .queue-head { padding: 1.2rem 1.4rem; border-bottom: 1px solid #f3e3d6; }
        .queue-title { font-size: 1.15rem; font-weight: 800; color: #23150d; margin: 0 0 0.3rem; }
        .queue-subtitle { margin: 0; color: #8b6349; font-size: 0.88rem; }
        .submission { padding: 1.2rem 1.4rem; border-bottom: 1px solid #f3e3d6; }
        .submission:last-child { border-bottom: none; }
        .submission h4 { margin: 0 0 0.35rem; color: #23150d; font-size: 1rem; font-weight: 800; }
        .meta { color: #8b6349; font-size: 0.84rem; margin-bottom: 0.75rem; }
        .body-copy { color: #33231a; font-size: 0.9rem; margin-bottom: 0.75rem; }
        .review-form { display: grid; gap: 0.8rem; }
        .review-textarea { width: 100%; min-height: 86px; border: 1px solid #ead8cb; border-radius: 12px; padding: 0.85rem 1rem; resize: vertical; }
        .review-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .approve-btn, .reject-btn { border: none; border-radius: 999px; padding: 0.75rem 1.15rem; font-weight: 800; cursor: pointer; color: white; }
        .approve-btn { background: #1d7f53; }
        .reject-btn { background: #b93838; }
        .empty-state { padding: 1.4rem; color: #8b6349; }
        .flash-message { background: #ecfdf3; border: 1px solid #9ad9b0; color: #146534; border-radius: 14px; padding: 0.95rem 1.1rem; font-weight: 700; }
        @media (max-width: 900px) {
            .queue-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="page-shell">
        @if(session('success'))
            <div class="flash-message">{{ session('success') }}</div>
        @endif

        <div class="page-card">
            <h1 class="page-title">Submission Review Queue</h1>
            <p class="page-copy">Review student-submitted skills and activities before they become part of the approved profile and search results.</p>
        </div>

        <div class="queue-grid">
            <section class="queue-card">
                <div class="queue-head">
                    <h2 class="queue-title">Pending Skills</h2>
                    <p class="queue-subtitle">{{ $pendingSkills->count() }} waiting for review</p>
                </div>

                @forelse($pendingSkills as $skill)
                    <div class="submission">
                        <h4>{{ $skill->skill_name }}</h4>
                        <div class="meta">{{ $skill->student->full_name }} · {{ $skill->student->student_id }} · {{ ucfirst($skill->proficiency_level) }}</div>
                        @if($skill->evidence_link)
                            <div class="body-copy"><a href="{{ $skill->evidence_link }}" target="_blank">Evidence link</a></div>
                        @endif
                        @if($skill->evidence_path)
                            <div class="body-copy"><a href="{{ asset('storage/'.$skill->evidence_path) }}" target="_blank">Evidence file</a></div>
                        @endif

                        <form method="POST" action="{{ route('submissions.skills.review', $skill) }}" class="review-form">
                            @csrf
                            @method('PATCH')
                            <textarea name="review_notes" class="review-textarea" placeholder="Optional review notes"></textarea>
                            <div class="review-actions">
                                <button type="submit" name="decision" value="approved" class="approve-btn">Approve</button>
                                <button type="submit" name="decision" value="rejected" class="reject-btn">Reject</button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="empty-state">No pending skill submissions.</div>
                @endforelse
            </section>

            <section class="queue-card">
                <div class="queue-head">
                    <h2 class="queue-title">Pending Activities</h2>
                    <p class="queue-subtitle">{{ $pendingActivities->count() }} waiting for review</p>
                </div>

                @forelse($pendingActivities as $activity)
                    <div class="submission">
                        <h4>{{ $activity->activity_name }}</h4>
                        <div class="meta">{{ $activity->student->full_name }} · {{ $activity->student->student_id }} · {{ $activity->date->format('M d, Y') }}</div>
                        @if($activity->description)
                            <div class="body-copy">{{ $activity->description }}</div>
                        @endif
                        @if($activity->evidence_link)
                            <div class="body-copy"><a href="{{ $activity->evidence_link }}" target="_blank">Evidence link</a></div>
                        @endif
                        @if($activity->evidence_path)
                            <div class="body-copy"><a href="{{ asset('storage/'.$activity->evidence_path) }}" target="_blank">Evidence file</a></div>
                        @endif

                        <form method="POST" action="{{ route('submissions.activities.review', $activity) }}" class="review-form">
                            @csrf
                            @method('PATCH')
                            <textarea name="review_notes" class="review-textarea" placeholder="Optional review notes"></textarea>
                            <div class="review-actions">
                                <button type="submit" name="decision" value="approved" class="approve-btn">Approve</button>
                                <button type="submit" name="decision" value="rejected" class="reject-btn">Reject</button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="empty-state">No pending activity submissions.</div>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
