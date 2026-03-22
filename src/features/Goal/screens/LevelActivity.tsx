import { COLORS } from '@/constants/colors';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import FooterGoal from '../components/FooterGoal';
import HeaderGoal from '../components/HeaderGoal';

const ACTIVITY_LEVELS = [
    { id: 'sedentary', label: 'Sédentaire', iconName: 'seat-recline-normal' },
    { id: 'lightly', label: 'Légèrement actif', iconName: 'walk' },
    { id: 'moderately', label: 'Modérément actif', iconName: 'run' },
    { id: 'very', label: 'Très actif', iconName: 'run-fast' },
] as const;

const LevelActivityScreen = () => {
    const insets = useSafeAreaInsets();
    const router = useRouter();
    const [selectedActivity, setSelectedActivity] = useState<string | null>(null);

    const handleNext = () => {
        if (selectedActivity) {
            // Placeholder for next screen route
            // router.push('/(goal)/next_screen');
            console.log('Selected Activity:', selectedActivity);
        }
    };

    return (
        <View style={[styles.container, { paddingTop: insets.top, paddingBottom: insets.bottom }]}>
            <HeaderGoal
                step={2}
                progress={2}
                totalSteps={3}
                title="Condition physique"
            />

            <ScrollView
                contentContainerStyle={styles.scrollContent}
                showsVerticalScrollIndicator={false}
            >
                <Text style={styles.title}>Quel est votre niveau{'\n'}d'activité ?</Text>

                <View style={styles.optionsContainer}>
                    {ACTIVITY_LEVELS.map((level) => {
                        const isSelected = selectedActivity === level.id;
                        return (
                            <TouchableOpacity
                                key={level.id}
                                style={[
                                    styles.optionButton,
                                    isSelected && styles.selectedOptionButton
                                ]}
                                onPress={() => setSelectedActivity(level.id)}
                                activeOpacity={0.8}
                            >
                                <MaterialCommunityIcons
                                    name={level.iconName as any}
                                    size={36}
                                    color="#000000"
                                    style={styles.icon}
                                />
                                <Text style={styles.optionText}>
                                    {level.label}
                                </Text>
                            </TouchableOpacity>
                        );
                    })}
                </View>
            </ScrollView>

            <FooterGoal
                onPress={handleNext}
                disabled={!selectedActivity}
                title="Sélectionner"
            />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#FFFFFF',
    },
    scrollContent: {
        alignItems: 'center',
        paddingHorizontal: 20,
        paddingTop: 40,
        paddingBottom: 20,
    },
    title: {
        fontSize: 28,
        fontWeight: '900',
        color: '#000000',
        textAlign: 'center',
        marginBottom: 40,
        lineHeight: 34,
    },
    optionsContainer: {
        width: '100%',
        gap: 15,
        paddingHorizontal: 10,
    },
    optionButton: {
        flexDirection: 'row',
        backgroundColor: '#D8D8D8',
        borderRadius: 15,
        paddingVertical: 15,
        paddingHorizontal: 30,
        width: '100%',
        alignItems: 'center',
        minHeight: 80,
    },
    selectedOptionButton: {
        backgroundColor: COLORS.primary,
    },
    icon: {
        marginRight: 20,
    },
    optionText: {
        fontSize: 18,
        fontWeight: '500',
        color: '#000000',
    },
});

export default LevelActivityScreen;
