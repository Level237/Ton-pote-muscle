import { mountBilanStepper } from './bilan-stepper';
import { mountLiveWorkout } from './live-workout';
import { mountShareSheet } from './share-sheet';

const liveRoot = document.getElementById('live-workout');
const bilanRoot = document.getElementById('bilan-stepper');
const shareRoot = document.getElementById('share-sheet');

if (liveRoot) {
    mountLiveWorkout(liveRoot);
}

if (bilanRoot) {
    mountBilanStepper(bilanRoot);
}

if (shareRoot) {
    mountShareSheet(shareRoot);
}
