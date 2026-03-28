import { COLORS } from '@/constants/colors';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import { Dimensions, ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import FooterGoal from '../components/FooterGoal';
import HeaderGoal from '../components/HeaderGoal';

const { width } = Dimensions.get('window');
const ITEM_WIDTH = (width - 40 - 20) / 3; // 40 horizontal padding for container, 20 gap total

const MOTIVATIONS = [
    { id: 'attract', label: 'Etre plus\nattirant', iconName: 'heart-outline' },
    { id: 'strength', label: 'Gagner\nen force', iconName: 'arm-flex-outline' },
    { id: 'confidence', label: 'Confiance\nen soi', iconName: 'handshake-outline' },
    { id: 'health', label: 'Améliorer\nma santé', iconName: 'heart-plus-outline' },
    { id: 'weight', label: 'Perte\nde poids', iconName: 'weight-kilogram' },
    { id: 'discipline1', label: 'Développer\nma discipline', iconName: 'certificate-outline' },
    { id: 'energy', label: 'Retrouver\nde l\'énergie', iconName: 'lightning-bolt-outline' },
    { id: 'example', label: 'Montrer\nl\'exemple', iconName: 'star-four-points-outline' },
    { id: 'discipline2', label: 'Développer\nma discipline', iconName: 'account-heart-outline' },
] as const;

const MotivationScreen = () => {
    const insets = useSafeAreaInsets();
    const router = useRouter();
    const [selectedMotivations, setSelectedMotivations] = useState<string[]>([]);

    const toggleSelection = (id: string) => {
        setSelectedMotivations((prev) => {
            if (prev.includes(id)) {
                return prev.filter((item) => item !== id);
            } else {
                return [...prev, id];
            }
        });
    };

    const handleNext = () => {
        if (selectedMotivations.length > 0) {
            router.push('/(home)');
        }
    };

    return (
        <View style={[styles.container, { paddingTop: insets.top, paddingBottom: insets.bottom }]}>
            <HeaderGoal
                step={2}
                progress={3}
                totalSteps={3}
                title="Condition physique"
            />

            <ScrollView
                contentContainerStyle={styles.scrollContent}
                showsVerticalScrollIndicator={false}
            >
                <Text style={styles.title}>Quelle est{'\n'}votre principale motivation{'\n'}avec cette application?</Text>

                <View style={styles.gridContainer}>
                    {MOTIVATIONS.map((motivation) => {
                        const isSelected = selectedMotivations.includes(motivation.id);
                        return (
                            <View key={motivation.id} style={styles.itemContainer}>
                                <TouchableOpacity
                                    style={[
                                        styles.circleContainer,
                                        isSelected && styles.selectedCircle
                                    ]}
                                    onPress={() => toggleSelection(motivation.id)}
                                    activeOpacity={0.8}
                                >
                                    <MaterialCommunityIcons
                                        name={motivation.iconName as any}
                                        size={40}
                                        color="#000000"
                                    />
                                </TouchableOpacity>
                                <Text style={styles.label}>{motivation.label}</Text>
                            </View>
                        );
                    })}
                </View>
            </ScrollView>

            <FooterGoal
                onPress={handleNext}
                disabled={selectedMotivations.length === 0}
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
        fontSize: 25,
        fontWeight: '900',
        color: '#000000',
        textAlign: 'center',
        marginBottom: 40,
        lineHeight: 32,
    },
    gridContainer: {
        flexDirection: 'row',
        flexWrap: 'wrap',
        justifyContent: 'center',
        gap: 10,
        width: '100%',
    },
    itemContainer: {
        width: ITEM_WIDTH,
        alignItems: 'center',
        marginBottom: 20,
    },
    circleContainer: {
        width: ITEM_WIDTH * 0.85,
        height: ITEM_WIDTH * 0.85,
        borderRadius: (ITEM_WIDTH * 0.85) / 2,
        backgroundColor: '#D8D8D8',
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: 10,
    },
    selectedCircle: {
        backgroundColor: COLORS.primary,
    },
    label: {
        fontSize: 13,
        fontWeight: '500',
        color: '#000000',
        textAlign: 'center',
    },
});

export default MotivationScreen;
