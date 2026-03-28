import { COLORS } from '@/constants/colors';
import { images } from '@/features/workout/constants/images';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import React from 'react';
import { ImageBackground, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

const HeroSection = () => {
    return (
        <View style={styles.container}>
            {/* Top Row: Hydration */}
            <View style={styles.hydrationContainer}>
                <MaterialCommunityIcons name="water" size={24} color="#5DADE2" />
                <Text style={styles.hydrationText}>Restez hydraté(e)</Text>
            </View>

            {/* Main Banner Card */}
            <TouchableOpacity activeOpacity={0.9} style={styles.bannerContainer}>
                <ImageBackground
                    source={images.hero}
                    style={styles.backgroundImage}
                    imageStyle={styles.imageStyle}
                >
                    {/* Dark Overlay for better contrast */}
                    <View style={styles.darkOverlay} />

                    {/* Yellow Corner Accents */}



                    {/* Content */}
                    <View style={styles.content}>
                        <Text style={styles.heroTitle}>CREER VOTRE</Text>
                        <Text style={styles.heroTitleHighlight}>PROPRE PROGRAMME</Text>

                        <TouchableOpacity style={styles.button} activeOpacity={0.8}>
                            <Text style={styles.buttonText}>COMMENCER</Text>
                        </TouchableOpacity>
                    </View>
                </ImageBackground>
            </TouchableOpacity>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        width: '100%',
        paddingHorizontal: 20,
        marginTop: 0,
        marginBottom: 30,
    },
    hydrationContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        marginBottom: 15,
    },
    hydrationText: {
        color: '#FFFFFF',
        fontSize: 18,
        fontWeight: 'bold',
        marginLeft: 8,
    },
    bannerContainer: {
        width: '100%',
        height: 200,
        borderRadius: 20,
        overflow: 'hidden',
    },
    backgroundImage: {
        flex: 1,
        justifyContent: 'center',
        padding: 20,
    },
    imageStyle: {
        borderRadius: 20,
    },
    darkOverlay: {
        ...StyleSheet.absoluteFillObject,
        backgroundColor: 'rgba(0,0,0,0.3)',
    },
    topLeftAccent: {
        position: 'absolute',
        top: -10,
        left: -10,
        width: 80,
        height: 60,
        backgroundColor: COLORS.primary,
        transform: [{ rotate: '-45deg' }],
        zIndex: 1,
    },
    bottomRightAccent: {
        position: 'absolute',
        bottom: -30,
        right: -30,
        width: 120,
        height: 100,
        backgroundColor: COLORS.primary,
        transform: [{ rotate: '-45deg' }],
        zIndex: 1,
    },
    content: {
        zIndex: 2,
    },
    heroTitle: {
        color: COLORS.primary,
        fontSize: 26,
        fontWeight: '900',
        lineHeight: 30,
    },
    heroTitleHighlight: {
        color: COLORS.primary,
        fontSize: 26,
        fontWeight: '900',
        lineHeight: 30,
        marginBottom: 15,
    },
    button: {
        backgroundColor: '#000000',
        paddingVertical: 10,
        paddingHorizontal: 25,
        borderRadius: 10,
        alignSelf: 'flex-start',
    },
    buttonText: {
        color: '#FFFFFF',
        fontSize: 14,
        fontWeight: 'bold',
    },
});

export default HeroSection;
