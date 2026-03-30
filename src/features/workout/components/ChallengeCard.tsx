import { COLORS } from '@/constants/colors';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { router } from 'expo-router';
import React from 'react';
import { Dimensions, ImageBackground, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

const { width } = Dimensions.get('window');
// Card width is 75% of screen width to allow peeking next items
const CARD_WIDTH = width * 0.7;
const CARD_HEIGHT = CARD_WIDTH;

interface ChallengeCardProps {
    title: string;
    duration: string;
    description: string;
    imageSource: any;
    onPress?: () => void;
}

const ChallengeCard: React.FC<ChallengeCardProps> = ({ title, duration, description, imageSource }) => {
    const handlePress = () => {
        router.push({
            pathname: '/(workout)/challenge-detail',
            params: { title, image: '' }
        });
    };

    return (
        <TouchableOpacity style={styles.cardContainer} activeOpacity={0.9} onPress={handlePress}>
            <ImageBackground
                source={imageSource}
                style={styles.imageBackground}
                imageStyle={styles.imageStyle}
            >
                {/* Dark Overlay for text readability */}
                <View style={styles.darkOverlay} />

                {/* Yellow Corner Shape (Curve) */}
                <View style={styles.cornerAccent} />

                {/* Content Container */}
                <View style={styles.contentContainer}>
                    <Text style={styles.title}>{title}</Text>
                    <Text style={styles.duration}>{duration}</Text>
                    <Text style={styles.description} numberOfLines={2}>
                        {description}
                    </Text>
                </View>

                {/* Play Button */}
                <View style={styles.playButtonWrapper}>
                    <View style={styles.playButton}>
                        <MaterialCommunityIcons name="play" size={32} color={COLORS.primary} style={{ marginLeft: 4 }} />
                    </View>
                </View>
            </ImageBackground>
        </TouchableOpacity>
    );
};

const styles = StyleSheet.create({
    cardContainer: {
        width: CARD_WIDTH,
        height: CARD_HEIGHT,
        borderRadius: 24,
        overflow: 'hidden',
        marginRight: 20, // Spacing between cards
        backgroundColor: '#1E1E1E', // Fallback color
    },
    imageBackground: {
        flex: 1,
        width: '100%',
        height: '100%',
    },
    imageStyle: {
        borderRadius: 24,
    },
    darkOverlay: {
        ...StyleSheet.absoluteFillObject,
        backgroundColor: 'rgba(0, 0, 0, 0.45)', // Uniform dark overlay
        borderRadius: 24,
    },
    cornerAccent: {
        position: 'absolute',
        bottom: -100,
        right: -80,
        width: 200,
        height: 200,
        borderRadius: 125,
        backgroundColor: COLORS.primary,
        opacity: 0.95,
    },
    contentContainer: {
        flex: 1,
        justifyContent: 'flex-end',
        padding: 24,
        paddingBottom: 30, // Extra padding to avoid play button overlap
        paddingRight: 80, // Prevent text from hitting the play button
    },
    title: {
        color: COLORS.primary,
        fontSize: 25,
        fontWeight: '900',

        marginBottom: 8,
    },
    duration: {
        color: '#FFFFFF',
        fontSize: 18,
        fontWeight: '600',
        marginBottom: 8,
    },
    description: {
        color: '#E0E0E0',
        fontSize: 14,
        lineHeight: 20,
    },
    playButtonWrapper: {
        position: 'absolute',
        bottom: 24,
        right: 24,
    },
    playButton: {
        width: 60,
        height: 60,
        borderRadius: 30,
        backgroundColor: '#000000',
        borderWidth: 3,
        borderColor: COLORS.primary,
        justifyContent: 'center',
        alignItems: 'center',
        elevation: 5,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 5,
    },
});

export default ChallengeCard;
