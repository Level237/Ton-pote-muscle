import { COLORS } from '@/constants/colors';
import { Image } from 'expo-image';
import React, { useState } from 'react';
import { StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import FooterGoal from '../components/FooterGoal';
import HeaderGoal from '../components/HeaderGoal';
import { imagesGoal } from '../constants/images';
const GenderGoalScreen = () => {
    const [gender, setGender] = useState<'masculin' | 'feminin' | null>(null);

    const handleNext = () => {
        if (gender) {
            console.log('Selected gender:', gender);
            // Future navigation logic
        }
    };

    return (
        <View style={styles.container}>
            <HeaderGoal
                step={1}
                totalSteps={3}
                title="A propos de votre corps"
            />

            <View style={styles.content}>
                <Text style={styles.title}>Quel est votre sexe ?</Text>

                <View style={styles.selectionContainer}>
                    <TouchableOpacity
                        style={[
                            styles.genderCircle,
                            gender === 'masculin' && styles.selectedCircle
                        ]}
                        onPress={() => setGender('masculin')}
                        activeOpacity={0.7}
                    >
                        <Image
                            source={imagesGoal.masculin}
                            style={styles.genderIcon}
                            contentFit="contain"
                        />
                    </TouchableOpacity>

                    <TouchableOpacity
                        style={[
                            styles.genderCircle,
                            gender === 'feminin' && styles.selectedCircle
                        ]}
                        onPress={() => setGender('feminin')}
                        activeOpacity={0.7}
                    >
                        <Image
                            source={imagesGoal.feminin}
                            style={styles.genderIcon}
                            contentFit="contain"
                        />
                    </TouchableOpacity>
                </View>
            </View>

            <FooterGoal
                onPress={handleNext}
                disabled={!gender}
            />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#FFFFFF',
    },
    content: {
        flex: 1,
        alignItems: 'center',
        paddingTop: 60,
    },
    title: {
        fontSize: 28,
        fontWeight: '900',
        color: '#000000',
        textAlign: 'center',
        marginBottom: 50,
    },
    selectionContainer: {
        flexDirection: 'column',
        alignItems: 'center',
        gap: 30,
    },
    genderCircle: {
        width: 150,
        height: 150,
        borderRadius: 75,
        backgroundColor: COLORS.primary,
        justifyContent: 'center',
        alignItems: 'center',
        borderWidth: 4,
        borderColor: 'transparent',
    },
    selectedCircle: {
        borderColor: '#2e0909ff',
    },
    genderIcon: {
        width: 80,
        height: 80,
    },
});

export default GenderGoalScreen;
