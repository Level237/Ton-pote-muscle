function pad(value) {
    return String(value).padStart(2, '0');
}

function formatTime(seconds) {
    const safe = Math.max(0, Math.ceil(seconds));

    return `${pad(Math.floor(safe / 60))}:${pad(safe % 60)}`;
}

function createSound() {
    let ctx = null;
    let enabled = true;

    const context = () => {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) {
            return null;
        }
        if (!ctx) {
            ctx = new AudioCtx();
        }
        if (ctx.state === 'suspended') {
            ctx.resume().catch(() => {});
        }

        return ctx;
    };

    const tone = (frequency, duration, delay = 0) => {
        if (!enabled) {
            return;
        }
        const audio = context();
        if (!audio) {
            return;
        }
        const start = audio.currentTime + delay;
        const osc = audio.createOscillator();
        const gain = audio.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(frequency, start);
        gain.gain.setValueAtTime(0.0001, start);
        gain.gain.exponentialRampToValueAtTime(0.14, start + 0.01);
        gain.gain.exponentialRampToValueAtTime(0.0001, start + duration);
        osc.connect(gain);
        gain.connect(audio.destination);
        osc.start(start);
        osc.stop(start + duration + 0.02);
    };

    return {
        isEnabled: () => enabled,
        toggle: () => {
            enabled = !enabled;
            if (enabled) {
                tone(660, 0.07);
            }

            return enabled;
        },
        step: () => {
            tone(587, 0.08);
            tone(880, 0.12, 0.09);
        },
        rest: () => {
            tone(523, 0.12);
        },
        tick: () => {
            tone(740, 0.06);
        },
        finish: () => {
            tone(523, 0.1);
            tone(659, 0.1, 0.11);
            tone(784, 0.18, 0.22);
        },
    };
}

export function mountLiveWorkout(root) {
    const payload = document.getElementById('live-session');
    const session = JSON.parse(payload.textContent);
    const exercises = session.exercises ?? [];
    const sound = createSound();
    const steps = Array.from(root.querySelectorAll('[data-live="step"]'));
    const els = {
        visual: root.querySelector('[data-live="visual"]'),
        panel: root.querySelector('[data-live="panel"]'),
        phase: root.querySelector('[data-live="phase"]'),
        title: root.querySelector('[data-live="title"]'),
        meta: root.querySelector('[data-live="meta"]'),
        gif: root.querySelector('[data-live="gif"]'),
        demo: root.querySelector('[data-live="demo"]'),
        timer: root.querySelector('[data-live="timer"]'),
        unit: root.querySelector('[data-live="unit"]'),
        upcoming: root.querySelector('[data-live="upcoming"]'),
        progress: root.querySelector('[data-live="progress"]'),
        pause: root.querySelector('[data-live="pause"]'),
        next: root.querySelector('[data-live="next"]'),
        skip: root.querySelector('[data-live="skip"]'),
        quit: root.querySelector('[data-live="quit"]'),
        sound: root.querySelector('[data-live="sound"]'),
        soundIcon: root.querySelector('[data-live="sound-icon"]'),
    };

    const tracks = exercises.map((exercise) => ({
        workout_exercise_id: exercise.workout_exercise_id,
        exercise_id: exercise.exercise_id,
        position: exercise.position,
        started_at: null,
        ended_at: null,
        skipped: false,
    }));

    let index = 0;
    let setIndex = 0;
    let phase = 'work';
    let paused = false;
    let remainingMs = 0;
    let lastBeepSecond = null;
    let lastTick = performance.now();
    let startedAt = Date.now();
    let pauseStarted = 0;
    let pausedMs = 0;
    let finishing = false;

    const current = () => exercises[index];
    const following = () => exercises[index + 1];

    const isTimedWork = () => {
        const exercise = current();

        return phase === 'work' && Number(exercise?.duration_seconds) > 0 && !exercise?.reps;
    };

    const isTimed = () => phase === 'rest' || isTimedWork();

    const markStart = () => {
        const track = tracks[index];
        if (track && !track.started_at) {
            track.started_at = new Date().toISOString();
        }
    };

    const markEnd = (skipped = false) => {
        const track = tracks[index];
        if (!track) {
            return;
        }
        track.ended_at = new Date().toISOString();
        track.skipped = skipped;
    };

    const nextLabel = () => {
        const exercise = current();
        const next = following();
        if (!exercise) {
            return '';
        }
        if (phase === 'rest') {
            if (setIndex < exercise.sets - 1) {
                return `À suivre : ${exercise.title} · série ${setIndex + 2}/${exercise.sets}`;
            }

            return next ? `À suivre : ${next.title} (${next.work_label})` : 'Dernier exercice';
        }
        if (setIndex < exercise.sets - 1) {
            return `Série ${setIndex + 1} sur ${exercise.sets}`;
        }

        return next ? `Suivant : ${next.title} (${next.work_label})` : 'Dernier exercice';
    };

    const paint = () => {
        const exercise = current();
        if (!exercise) {
            return;
        }

        const rest = phase === 'rest';
        const showGif = Boolean(!rest && exercise.gif);

        root.classList.toggle('bg-primary', rest);
        root.classList.toggle('text-bg', rest);
        document.body.classList.toggle('bg-primary', rest);
        document.body.classList.toggle('text-bg', rest);

        steps.forEach((bar, position) => {
            if (rest) {
                bar.className = position <= index
                    ? 'h-1 flex-1 rounded-full bg-bg'
                    : 'h-1 flex-1 rounded-full bg-bg/25';

                return;
            }
            bar.className = position < index
                ? 'h-1 flex-1 rounded-full bg-primary'
                : position === index
                    ? 'h-1 flex-1 rounded-full bg-text'
                    : 'h-1 flex-1 rounded-full bg-border';
        });

        els.phase.textContent = rest ? 'Repos' : `Exercice ${index + 1} sur ${exercises.length}`;
        els.phase.className = rest
            ? 'text-xs font-bold uppercase tracking-wider text-bg/70'
            : 'text-xs font-bold uppercase tracking-wider text-primary';
        els.title.textContent = rest ? 'Récupère' : exercise.title;
        els.title.className = rest
            ? 'mt-2 text-2xl font-bold text-bg sm:text-3xl'
            : 'mt-2 text-2xl font-bold text-text sm:text-3xl';
        els.meta.textContent = rest
            ? (setIndex < exercise.sets - 1 ? exercise.title : following()?.title || exercise.title)
            : `${exercise.work_label} · série ${setIndex + 1}/${exercise.sets}`;
        els.meta.className = rest ? 'mt-1 text-sm text-bg/75' : 'mt-1 text-sm text-muted';
        els.progress.textContent = `${index + 1} / ${exercises.length}`;
        els.progress.className = rest
            ? 'text-xs font-semibold tabular-nums text-bg/75'
            : 'text-xs font-semibold tabular-nums text-muted';

        if (showGif) {
            if (els.gif.getAttribute('src') !== exercise.gif) {
                els.gif.src = exercise.gif;
            }
            els.visual.classList.remove('hidden');
            els.visual.classList.add('flex');
            els.panel.className = 'flex flex-col items-center text-center lg:col-span-6 lg:items-start lg:text-left';
        } else {
            els.gif.removeAttribute('src');
            els.visual.classList.add('hidden');
            els.visual.classList.remove('flex');
            els.panel.className = 'flex flex-col items-center text-center lg:col-span-12';
        }

        if (!rest && exercise.video) {
            els.demo.href = exercise.video;
            els.demo.classList.remove('hidden');
            els.demo.classList.toggle('text-primary', true);
            els.demo.classList.toggle('hover:text-primary-hover', true);
        } else {
            els.demo.removeAttribute('href');
            els.demo.classList.add('hidden');
        }

        if (isTimed()) {
            els.timer.textContent = formatTime(remainingMs / 1000);
            els.unit.textContent = rest ? 'secondes de repos' : 'secondes';
        } else if (exercise.reps) {
            els.timer.textContent = `× ${exercise.reps}`;
            els.unit.textContent = 'répétitions';
        } else {
            els.timer.textContent = formatTime((Date.now() - startedAt - pausedMs) / 1000);
            els.unit.textContent = '';
        }

        els.timer.classList.toggle('text-bg', rest);
        els.unit.className = rest
            ? 'mt-2 text-xs uppercase tracking-wider text-bg/70'
            : 'mt-2 text-xs uppercase tracking-wider text-muted';
        els.upcoming.textContent = nextLabel();
        els.upcoming.className = rest
            ? 'mb-3 truncate text-center text-xs text-bg/75'
            : 'mb-3 truncate text-center text-xs text-muted';

        els.pause.textContent = paused ? 'Reprendre' : 'Pause';
        els.pause.className = rest
            ? 'inline-flex min-h-12 items-center justify-center rounded-xl border border-bg/25 px-4 text-sm font-semibold text-bg'
            : 'inline-flex min-h-12 items-center justify-center rounded-xl border border-border bg-surface px-4 text-sm font-semibold text-text';

        const lastStep = !rest && setIndex >= exercise.sets - 1 && index >= exercises.length - 1;
        els.next.textContent = rest ? 'Passer le repos' : lastStep ? 'Terminer' : 'Suivant';
        els.next.className = rest
            ? 'inline-flex min-h-12 items-center justify-center rounded-xl bg-bg px-4 text-sm font-bold text-primary'
            : 'inline-flex min-h-12 items-center justify-center rounded-xl bg-primary px-4 text-sm font-bold text-bg';

        els.quit.className = rest
            ? 'min-h-11 px-1 text-sm font-medium text-bg/80 hover:text-bg'
            : 'min-h-11 px-1 text-sm font-medium text-muted hover:text-text';
        els.skip.className = rest
            ? 'min-h-11 px-1 text-sm font-medium text-bg/80 hover:text-bg'
            : 'min-h-11 px-1 text-sm font-medium text-muted hover:text-text';
        els.sound.className = rest
            ? 'flex h-11 w-11 items-center justify-center rounded-lg text-sm text-bg/80 hover:text-bg'
            : 'flex h-11 w-11 items-center justify-center rounded-lg text-sm text-muted hover:text-text';
    };

    const armTimer = () => {
        const exercise = current();
        lastBeepSecond = null;
        if (phase === 'rest') {
            remainingMs = (exercise.rest_seconds || 0) * 1000;
            if (remainingMs <= 0) {
                advance();
                return;
            }
        } else if (isTimedWork()) {
            remainingMs = Number(exercise.duration_seconds) * 1000;
        } else {
            remainingMs = 0;
        }
        paint();
    };

    const finish = async (abandoned) => {
        if (finishing) {
            return;
        }
        finishing = true;
        if (!abandoned) {
            sound.finish();
        }

        const response = await fetch(session.completeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': session.csrf,
            },
            body: JSON.stringify({
                abandoned,
                actual_duration_seconds: Math.max(
                    0,
                    Math.round((Date.now() - startedAt - pausedMs) / 1000),
                ),
                exercises: tracks,
            }),
        });

        const payload = await response.json().catch(() => ({}));
        window.location.href = payload.redirect || '/dashboard';
    };

    const goNextExercise = () => {
        if (index >= exercises.length - 1) {
            finish(false);
            return;
        }
        index += 1;
        setIndex = 0;
        phase = 'work';
        sound.step();
        markStart();
        armTimer();
    };

    const advance = () => {
        const exercise = current();
        if (!exercise) {
            finish(false);
            return;
        }

        if (phase === 'work') {
            const lastSet = setIndex >= exercise.sets - 1;

            if (!lastSet) {
                if (exercise.rest_seconds > 0) {
                    phase = 'rest';
                    sound.rest();
                    armTimer();
                    return;
                }
                setIndex += 1;
                sound.step();
                armTimer();
                return;
            }

            markEnd(false);
            if (index >= exercises.length - 1) {
                finish(false);
                return;
            }
            if (exercise.rest_seconds > 0) {
                phase = 'rest';
                sound.rest();
                armTimer();
                return;
            }
            goNextExercise();
            return;
        }

        if (setIndex < exercise.sets - 1) {
            setIndex += 1;
            phase = 'work';
            sound.step();
            armTimer();
            return;
        }

        goNextExercise();
    };

    const skip = () => {
        markEnd(true);
        goNextExercise();
    };

    els.next.addEventListener('click', () => advance());
    els.skip.addEventListener('click', () => skip());
    els.pause.addEventListener('click', () => {
        paused = !paused;
        if (paused) {
            pauseStarted = performance.now();
        } else if (pauseStarted) {
            pausedMs += performance.now() - pauseStarted;
        }
        paint();
    });
    els.sound.addEventListener('click', () => {
        const on = sound.toggle();
        els.soundIcon.className = on
            ? 'fa-solid fa-volume-high'
            : 'fa-solid fa-volume-xmark';
    });
    els.quit.addEventListener('click', () => finish(true));

    const tick = (now) => {
        if (!paused && isTimed()) {
            remainingMs -= now - lastTick;
            const sec = Math.ceil(remainingMs / 1000);
            if (sec >= 1 && sec <= 3 && sec !== lastBeepSecond) {
                lastBeepSecond = sec;
                sound.tick();
            }
            if (remainingMs <= 0) {
                remainingMs = 0;
                advance();
            } else {
                paint();
            }
        } else if (!paused && !isTimed()) {
            paint();
        }
        lastTick = now;
        requestAnimationFrame(tick);
    };

    if (exercises.length === 0) {
        finish(false);
        return;
    }

    markStart();
    armTimer();
    requestAnimationFrame(tick);
}
