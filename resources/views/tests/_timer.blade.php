@php
    $initialMinutes = floor($durationSeconds / 60);
    $initialSeconds = $durationSeconds % 60;
@endphp

<div class="test-timer-panel" data-test-timer-panel>
    <div>
        <span class="eyebrow">Thời gian làm bài</span>
        <strong data-test-timer>{{ sprintf('%02d:%02d', $initialMinutes, $initialSeconds) }}</strong>
    </div>
    <p data-test-timer-status>Hết giờ hệ thống sẽ tự động nộp bài và chấm điểm.</p>
</div>
