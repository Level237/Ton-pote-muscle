import { COLORS } from '@/constants/colors';
import React from 'react';
import { StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
interface HeaderGoalProps {
    step: number;
    progress: number;
    totalSteps: number;
    title: string;
}

const HeaderGoal = ({ step, progress, totalSteps, title }: HeaderGoalProps) => {
    const insets = useSafeAreaInsets();

    return (
        <View style={[styles.container, { paddingTop: insets.top + 20 }]}>
            <View style={styles.stepIndicator}>
                <View style={styles.stepCircle}>
                    <Text style={styles.stepNumber}>{step}</Text>
                </View>
                <Text style={styles.stepTitle}>{title}</Text>
            </View>

            <View style={styles.progressContainer}>
                {Array.from({ length: totalSteps }).map((_, i) => (
                    <View
                        key={i}
                        style={[
                            styles.progressBar,
                            { backgroundColor: i < progress ? COLORS.primary : '#E0E0E0' }
                        ]}
                    />
                ))}
            </View>

            <View style={styles.divider} />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        width: '100%',
        paddingHorizontal: 25,
        backgroundColor: '#FFFFFF',
    },
    stepIndicator: {
        flexDirection: 'row',
        alignItems: 'center',
        marginBottom: 15,
    },
    stepCircle: {
        width: 24,
        height: 24,
        borderRadius: 12,
        backgroundColor: COLORS.primary,
        justifyContent: 'center',
        alignItems: 'center',
        marginRight: 10,
    },
    stepNumber: {
        fontSize: 14,
        fontWeight: '900',
        color: '#000000',
    },
    stepTitle: {
        fontSize: 16,
        fontWeight: '700',
        color: '#000000',
    },
    progressContainer: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginBottom: 20,
    },
    progressBar: {
        flex: 1,
        height: 4,
        borderRadius: 2,
        marginHorizontal: 3,
    },
    divider: {
        height: 1,
        backgroundColor: COLORS.primary,
        width: '100%',
    },
});

export default HeaderGoal;
