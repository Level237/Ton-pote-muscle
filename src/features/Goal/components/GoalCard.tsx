import { COLORS } from '@/constants/colors';
import { Image } from 'expo-image';
import React from 'react';
import { StyleSheet, Text, TouchableOpacity, View } from 'react-native';

interface GoalCardProps {
    title: string;
    image: any;
    isSelected: boolean;
    onPress: () => void;
}

const GoalCard = ({ title, image, isSelected, onPress }: GoalCardProps) => {
    return (
        <TouchableOpacity
            style={[
                styles.card,
                isSelected && styles.selectedCard
            ]}
            onPress={onPress}
            activeOpacity={0.8}
        >
            <View style={styles.content}>
                <Text style={styles.title}>{title}</Text>
            </View>
            <View style={styles.imageWrapper} pointerEvents="none">
                <Image
                    source={image}
                    style={styles.image}
                    contentFit="contain"
                />
            </View>
        </TouchableOpacity>
    );
};

const styles = StyleSheet.create({
    card: {
        backgroundColor: COLORS.primary,
        borderRadius: 20,
        zIndex: 1,
        height: 130, // Controlled height
        width: '100%',
        marginTop: 20, // Space for the popping head
        marginBottom: 10,
        borderWidth: 4,
        borderColor: 'transparent',
        position: 'relative',
        // Note: overflow is visible by default, allowing the wrapper to extend top
    },
    selectedCard: {
        borderColor: '#000000',
    },
    content: {
        flex: 1,
        flexDirection: 'row',
        alignItems: 'center',
        paddingLeft: 20,
        zIndex: 2, // Keep text on top
    },
    title: {
        fontSize: 20,
        fontWeight: '900',
        color: '#000000',
        width: '60%',
        lineHeight: 26,
    },
    imageWrapper: {
        position: 'absolute',
        right: 0,
        bottom: -1,
        width: 180,
        height: 150, // Taller than the card to allow head overlap
        overflow: 'hidden', // Clips anything extending past its Bottom/Sides
        borderBottomRightRadius: 1, // Matches the card's inner radius
        zIndex: 10,
    },
    image: {
        width: '100%',
        height: '100%',
        position: 'absolute',
        bottom: -3, // Slight offset to align torso with bottom
    },
});

export default GoalCard;
