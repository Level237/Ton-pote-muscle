import { COLORS } from '@/constants/colors';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import FooterGoal from '../components/FooterGoal';
import HeaderGoal from '../components/HeaderGoal';

const TRAINING_LEVELS = [
    { id: 'new', label: "C'est nouveau pour moi" },
    { id: 'start-stop', label: "Je commence toujours\nmais je ne termine jamais" },
    { id: 'hard', label: "Faire du sport, c'est un calvaire" },
    { id: 'seasonal', label: "Je suis saisonnier\npour des objectifs precis" },
    { id: 'regular', label: "Je suis assez régulier" },
];

const LevelTrainingScreen = () => {
    const insets = useSafeAreaInsets();
    const router = useRouter();
    const [selectedLevel, setSelectedLevel] = useState<string | null>(null);

    const handleNext = () => {
        if (selectedLevel) {
            // router.push('/(goal)/next_screen');
            console.log('Selected Level:', selectedLevel);
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
                <Text style={styles.title}>Quel est votre niveau{'\n'}d'entrainement ?</Text>

                <View style={styles.optionsContainer}>
                    {TRAINING_LEVELS.map((level) => {
                        const isSelected = selectedLevel === level.id;
                        return (
                            <TouchableOpacity
                                key={level.id}
                                style={[
                                    styles.optionButton,
                                    isSelected && styles.selectedOptionButton
                                ]}
                                onPress={() => setSelectedLevel(level.id)}
                                activeOpacity={0.8}
                            >
                                <Text style={[
                                    styles.optionText,
                                    isSelected && styles.selectedOptionText
                                ]}>
                                    {level.label}
                                </Text>
                            </TouchableOpacity>
                        );
                    })}
                </View>
            </ScrollView>

            <FooterGoal
                onPress={handleNext}
                disabled={!selectedLevel}
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
        backgroundColor: '#E0E0E0',
        borderRadius: 15,
        paddingVertical: 20,
        paddingHorizontal: 20,
        width: '100%',
        justifyContent: 'center',
        alignItems: 'center',
        minHeight: 80,
        borderWidth: 2,
        borderColor: 'transparent',
    },
    selectedOptionButton: {

        backgroundColor: COLORS.primary,
    },
    optionText: {
        fontSize: 16,
        fontWeight: '600',
        color: '#000000',
        textAlign: 'center',
        lineHeight: 22,
    },
    selectedOptionText: {
        fontWeight: '800',
    },
});

export default LevelTrainingScreen;
