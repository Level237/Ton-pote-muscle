import ChallengesSection from '@/features/workout/components/ChallengesSection';
import HeaderWorkout from '@/features/workout/components/HeaderWorkout';
import HeroSection from '@/features/workout/components/HeroSection';
import ProgramSection from '@/features/workout/components/ProgramSection';
import TargetedExerciseSection from '@/features/workout/components/TargetedExerciseSection';
import WeeklyRecap from '@/features/workout/components/WeeklyRecap';
import { StatusBar } from 'expo-status-bar';
import React from 'react';
import { ScrollView, StyleSheet, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

export default function HomeScreen() {
    const insets = useSafeAreaInsets();

    return (
        <View style={[styles.container, { paddingTop: insets.top }]}>
            <StatusBar style="light" hidden={true} />
            {/* Static Header */}
            <HeaderWorkout />

            <ScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={styles.scrollContent}
            >
                <WeeklyRecap />

                <ChallengesSection />
                <TargetedExerciseSection />
                <HeroSection />
                <ProgramSection />


                {/* Adding padding at the bottom to account for the custom floating bottom tab bar */}
                <View style={{ height: 120 }} />
            </ScrollView>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#000000',
    },
    scrollContent: {
        flexGrow: 1,
    },
});
