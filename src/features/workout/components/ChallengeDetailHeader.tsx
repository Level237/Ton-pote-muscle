import { COLORS } from '@/constants/colors';
import { images } from '@/constants/images';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { router } from 'expo-router';
import React from 'react';
import { Dimensions, Image, ImageBackground, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

const { width } = Dimensions.get('window');

interface ChallengeDetailHeaderProps {
    title: string;
    image?: any;
}

const ChallengeDetailHeader: React.FC<ChallengeDetailHeaderProps> = ({ title, image }) => {
    const insets = useSafeAreaInsets();

    return (
        <View style={[styles.container, { paddingTop: insets.top }]}>
            {/* Black Background Container */}
            <View style={styles.identityRow}>
                <View style={styles.logoGroup}>
                    <Image source={images.logo} style={styles.logo} resizeMode="contain" />
                    <Text style={styles.brandName}>Ton Pote Musclé</Text>
                </View>
                <View style={styles.flagBadge}>
                    <Text style={{ fontSize: 14 }}>🇫🇷</Text>
                </View>
            </View>

            <View style={styles.heroContent}>
                <View style={styles.titleContainer}>
                    <TouchableOpacity
                        style={styles.backFab}
                        onPress={() => router.back()}
                        activeOpacity={0.8}
                    >
                        <MaterialCommunityIcons name="arrow-left" size={24} color="#000000" />
                    </TouchableOpacity>
                    <Text style={styles.mainTitle}>{title}</Text>

                    {/* Small Decorative Icons */}
                    <View style={styles.miniStats}>
                        <MaterialCommunityIcons name="fire" size={16} color="#FFFFFF" style={{ marginRight: 8 }} />
                        <MaterialCommunityIcons name="lightning-bolt" size={16} color="#FFFFFF" style={{ marginRight: 8 }} />
                        <MaterialCommunityIcons name="timer-outline" size={16} color="#FFFFFF" />
                    </View>
                </View>

                {/* Styled Image Section */}
                <View style={styles.imageWrapper}>
                    <ImageBackground
                        source={image || require('@/assets/images/workout1.jpg')}
                        style={styles.heroImage}
                        imageStyle={styles.imageStyle}
                    >
                        {/* Gradient or Shape overlay could go here */}
                        <View style={styles.imageOverlayAccent} />
                    </ImageBackground>
                </View>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        width: '100%',
        backgroundColor: '#000000',
        paddingBottom: 40,
    },
    identityRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingHorizontal: 20,
        paddingVertical: 10,
    },
    logo: {
        width: 30,
        height: 30,
        marginRight: 1,
    },
    logoGroup: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    brandName: {
        color: '#FFFFFF',
        fontSize: 14,
        fontWeight: '700',
        marginLeft: 6,
    },
    flagBadge: {
        width: 24,
        height: 24,
        borderRadius: 12,
        backgroundColor: '#333333',
        alignItems: 'center',
        justifyContent: 'center',
    },
    heroContent: {
        flexDirection: 'row',
        paddingHorizontal: 20,
        alignItems: 'flex-end',
        justifyContent: 'space-between',
        marginTop: 10,
    },
    titleContainer: {
        flex: 1.2,
        zIndex: 10,
    },
    backFab: {
        width: 36,
        height: 36,
        borderRadius: 18,
        backgroundColor: COLORS.primary,
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: 12,
    },
    mainTitle: {
        color: COLORS.primary,
        fontSize: 34,
        fontWeight: '900',
        lineHeight: 38,
        textTransform: 'uppercase',
    },
    miniStats: {
        flexDirection: 'row',
        marginTop: 10,
        opacity: 0.6,
    },
    imageWrapper: {
        flex: 1,
        height: 180,
        marginLeft: -40, // Overlap effect
        marginBottom: -10,
    },
    heroImage: {
        width: '120%',
        height: '100%',
        justifyContent: 'flex-end',
    },
    imageStyle: {
        borderRadius: 20,
        resizeMode: 'cover',
    },
    imageOverlayAccent: {
        position: 'absolute',
        bottom: 0,
        left: 0,
        width: '100%',
        height: '30%',
        backgroundColor: 'rgba(0,0,0,0.4)',
    },
});

export default ChallengeDetailHeader;
