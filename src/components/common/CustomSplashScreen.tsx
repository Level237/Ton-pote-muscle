import { images } from '@/constants/images';
import React from 'react';
import { ActivityIndicator, Dimensions, Image, ImageBackground, StyleSheet, View } from 'react-native';

const { width, height } = Dimensions.get('window');

const CustomSplashScreen = () => {
    return (
        <View style={styles.container}>
            <ImageBackground
                source={images.splash}
                style={styles.backgroundImage}
                resizeMode="cover"
            >
                <View style={styles.overlay}>
                    <Image
                        source={images.logo}
                        style={styles.logo}
                        resizeMode="contain"
                    />
                    <View style={styles.loaderContainer}>
                        <ActivityIndicator size="large" color="#FFFFFF" />
                    </View>
                </View>
            </ImageBackground>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
    },
    backgroundImage: {
        width: width,
        height: height,
    },
    overlay: {
        flex: 1,
        backgroundColor: 'rgba(0, 0, 0, 0.4)', // Dark overlay as seen in the reference
        justifyContent: 'center',
        alignItems: 'center',
    },
    logo: {
        width: width * 0.6,
        height: width * 0.6,
    },
    loaderContainer: {
        position: 'absolute',
        bottom: height * 0.15, // Position near the bottom as in reference
    },
});

export default CustomSplashScreen;
