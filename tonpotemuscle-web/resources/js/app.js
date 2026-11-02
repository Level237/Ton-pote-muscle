import { mountBilanStepper } from './bilan-stepper';
import { mountLiveWorkout } from './live-workout';

const liveRoot = document.getElementById('live-workout');
const bilanRoot = document.getElementById('bilan-stepper');

if (liveRoot) {
    mountLiveWorkout(liveRoot);
}

if (bilanRoot) {
    mountBilanStepper(bilanRoot);
}
