import { COLORS } from '@/constants/colors';
import ChallengeDetailStaticHeader from '@/features/workout/components/ChallengeDetailStaticHeader';
import ChallengeHeroSection from '@/features/workout/components/ChallengeHeroSection';
import ChallengeStatsCard from '@/features/workout/components/ChallengeStatsCard';
import ChallengeTimeline from '@/features/workout/components/ChallengeTimeline';

import { useLocalSearchParams } from 'expo-router';
import React from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

export default function ChallengeDetailScreen() {
    const { title, image } = useLocalSearchParams<{ title: string; image: string }>();

    return (
        <View style={styles.container}>
            {/* 1. Static Top Navigation & Identity */}
            <ChallengeDetailStaticHeader />

            <ScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={styles.scrollContent}
            >
                {/* 2. Scrollable Hero Section (Title + Image) */}
                <ChallengeHeroSection title={title || "Défi"} />

                {/* 3. Stats & Timeline Details */}
                <View style={styles.detailsContainer}>
                    <ChallengeStatsCard />

                    <View style={styles.timelineWrapper}>
                        <ChallengeTimeline />
                    </View>
                </View>

                {/* Bottom Padding for the button */}
                <View style={{ height: 100 }} />
            </ScrollView>

            {/* Fixed Footer Button */}
            <View style={styles.footer}>
                <TouchableOpacity style={styles.startButton} activeOpacity={0.8}>
                    <Text style={styles.startButtonText}>commencer maintenant</Text>
                </TouchableOpacity>
            </View>
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
    detailsContainer: {
        flex: 1,
        backgroundColor: '#E5E5E5', // Light grey background as seen in the image
        marginTop: -30, // Pull up to overlap with header
        borderTopLeftRadius: 30,
        borderTopRightRadius: 30,
        paddingTop: 60, // Space for the floating stats card
        paddingHorizontal: 20,
    },
    timelineWrapper: {
        marginTop: 20,
    },
    footer: {
        position: 'absolute',
        bottom: 30,
        left: 20,
        right: 20,
        alignItems: 'center',
    },
    startButton: {
        backgroundColor: COLORS.primary,
        paddingVertical: 18,
        paddingHorizontal: 40,
        borderRadius: 20,
        width: '100%',
        alignItems: 'center',
        elevation: 5,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 5,
    },
    startButtonText: {
        color: '#000000',
        fontSize: 18,
        fontWeight: '900',
        textTransform: 'lowercase',
    },
});
