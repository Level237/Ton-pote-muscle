import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import { ScrollView, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import FooterGoal from '../components/FooterGoal';
import GoalCard from '../components/GoalCard';
import HeaderGoal from '../components/HeaderGoal';
import { imagesGoal } from '../constants/images';

const OBJECTIVES = [
    {
        id: 'weight-loss',
        title: 'Perte\nde poids',
        image: imagesGoal.goalMen1,
    },
    {
        id: 'muscle-gain',
        title: 'Renforcement\nmusculaire',
        image: imagesGoal.goalMen2,
    },
    {
        id: 'fitness',
        title: 'Remise\nen forme',
        image: imagesGoal.goalMen3,
    },
];

const ObjectiveGoalScreen = () => {
    const insets = useSafeAreaInsets();
    const router = useRouter();
    const [selectedObjective, setSelectedObjective] = useState<string | null>(null);

    const handleNext = () => {
        if (selectedObjective) {
            // router.push('/(goal)/focus');
            console.log('Selected Objective:', selectedObjective);
        }
    };

    return (
        <View style={[styles.container, { paddingTop: insets.top, paddingBottom: insets.bottom }]}>
            <HeaderGoal
                step={2}
                progress={1}
                totalSteps={3}
                title="Objectifs et Focus"
            />

            <ScrollView
                contentContainerStyle={styles.scrollContent}
                showsVerticalScrollIndicator={false}
            >
                <Text style={styles.title}>Quel est votre objectif ?</Text>

                <View style={styles.cardsContainer}>
                    {OBJECTIVES.map((obj) => (
                        <GoalCard
                            key={obj.id}
                            title={obj.title}
                            image={obj.image}
                            isSelected={selectedObjective === obj.id}
                            onPress={() => setSelectedObjective(obj.id)}
                        />
                    ))}
                </View>
            </ScrollView>

            <FooterGoal
                onPress={handleNext}
                disabled={!selectedObjective}
            />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        marginTop: -32,
        backgroundColor: '#FFFFFF',
    },
    scrollContent: {
        alignItems: 'center',
        paddingHorizontal: 20,
        paddingTop: 10,
        paddingBottom: 20,
    },
    title: {
        fontSize: 25,
        fontWeight: '900',
        color: '#000000',
        textAlign: 'center',
        marginBottom: 20
    },
    cardsContainer: {
        width: '100%',
    },
});

export default ObjectiveGoalScreen;
