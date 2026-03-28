import { COLORS } from '@/constants/colors';
import React from 'react';
import { Image, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

interface TargetedExerciseCardProps {
    title: string;
    imageSource: any;
    onPress?: () => void;
}

const TargetedExerciseCard: React.FC<TargetedExerciseCardProps> = ({ title, imageSource, onPress }) => {
    return (
        <TouchableOpacity style={styles.card} activeOpacity={0.8} onPress={onPress}>
            <View style={styles.imageContainer}>
                <Image source={imageSource} style={styles.image} />
            </View>
            <Text style={styles.title}>{title}</Text>
        </TouchableOpacity>
    );
};

const styles = StyleSheet.create({
    card: {
        backgroundColor: COLORS.primary,
        width: '47.5%', // Slightly less than 50% to account for gap
        aspectRatio: 1.1,
        borderRadius: 20,
        padding: 15,
        marginBottom: 15,
        alignItems: 'flex-start',
        justifyContent: 'center',
        position: 'relative',
        overflow: 'hidden',
    },
    imageContainer: {
        position: 'absolute',
        top: 10,
        right: 10,
        opacity: 0.9,
    },
    image: {
        width: 60,
        height: 60,
    },
    title: {
        color: '#000000',
        fontSize: 18,
        fontWeight: 'bold',
        marginTop: 40, // Push down to make room for icon
    },
});

export default TargetedExerciseCard;
