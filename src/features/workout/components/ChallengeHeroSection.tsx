import { COLORS } from '@/constants/colors';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import React from 'react';
import { ImageBackground, StyleSheet, Text, View } from 'react-native';

interface ChallengeHeroSectionProps {
    title: string;
    image?: any;
}

const ChallengeHeroSection: React.FC<ChallengeHeroSectionProps> = ({ title, image }) => {
    return (
        <View style={styles.container}>
            <ImageBackground
                source={image || require('@/assets/images/workout1.jpg')}
                style={styles.heroImage}
                imageStyle={styles.imageStyle}
            >
                <View style={styles.overlay} />

                <View style={styles.content}>
                    <Text style={styles.mainTitle}>{title}</Text>

                    <View style={styles.miniStats}>
                        <View style={styles.iconCircle}>
                            <MaterialCommunityIcons name="fire" size={12} color="#000" />
                        </View>
                        <View style={styles.iconCircle}>
                            <MaterialCommunityIcons name="lightning-bolt" size={12} color="#000" />
                        </View>
                        <View style={styles.iconCircle}>
                            <MaterialCommunityIcons name="timer-outline" size={12} color="#000" />
                        </View>
                    </View>
                </View>

                {/* Diagonal Accent at the bottom right */}
                <View style={styles.diagonalAccent} />
            </ImageBackground>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        width: '100%',
        backgroundColor: '#000000',
        height: 250,
    },
    heroImage: {
        flex: 1,
        width: '100%',
        height: '100%',
        justifyContent: 'center',
    },
    imageStyle: {
        resizeMode: 'cover',
    },
    overlay: {
        ...StyleSheet.absoluteFillObject,
        backgroundColor: 'rgba(0,0,0,0.3)',
    },
    content: {
        paddingHorizontal: 25,
        zIndex: 10,
    },
    mainTitle: {
        color: COLORS.primary,
        fontSize: 30,
        fontWeight: '900',
        lineHeight: 35,
        width: '60%',
        textTransform: 'capitalize', // To match "Corps complet"
    },
    miniStats: {
        flexDirection: 'row',
        marginTop: 15,
    },
    iconCircle: {
        width: 24,
        height: 24,
        borderRadius: 12,
        backgroundColor: COLORS.primary,
        justifyContent: 'center',
        alignItems: 'center',
        marginRight: 8,
    },
    diagonalAccent: {
        position: 'absolute',
        bottom: -50,
        right: -60,
        width: 200,
        height: 150,
        backgroundColor: COLORS.primary,
        transform: [{ rotate: '-35deg' }],
    },
});

export default ChallengeHeroSection;
