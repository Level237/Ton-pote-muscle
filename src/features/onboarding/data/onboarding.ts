import { imageWelcome } from '../constants/image';

export interface OnboardingData {
    id: number;
    title: string;
    image: any;
    background: any;
}

export const ONBOARDING_DATA: OnboardingData[] = [
    {
        id: 1,
        title: "Une nouvelle expérience de l'entrainement à domicile",
        image: imageWelcome.onboarding1,
        background: imageWelcome.bg1,
    },
    {
        id: 2,
        title: "Des résultats visibles, adaptés à ton rythme.",
        image: imageWelcome.onboarding2,
        background: imageWelcome.bg2,
    },
    {
        id: 3,
        title: "Ton corps Ton rythme",
        image: imageWelcome.onboarding3,
        background: imageWelcome.bg3,
    },
];
