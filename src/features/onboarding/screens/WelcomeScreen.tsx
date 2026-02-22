import { images } from '@/constants/images';
import React from 'react';
import { Dimensions, Image, ImageBackground, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
const { width, height } = Dimensions.get('window');


const WelcomeScreen = () => {
    const insets = useSafeAreaInsets();

    return (
        <View style={styles.container}>
            <ImageBackground
                source={images.habillage}
                style={styles.backgroundImage}
                resizeMode="cover"
            >
                <View style={[styles.overlay, { paddingTop: insets.top, paddingBottom: insets.bottom }]}>
                    <View style={styles.content}>
                        <Text style={styles.title}>
                            Bienvenue{"\n"}
                            <Text style={styles.subtitle}>sur votre application</Text>{"\n"}
                            <Text style={styles.brand}>TonPoteMusclé</Text>
                        </Text>

                        <Text style={styles.description}>
                            Lorem ipsum dolor sit amet,{"\n"}
                            consectetur adipiscing elit,
                        </Text>

                        <Image
                            source={images.logo2}
                            style={styles.logo}
                            resizeMode="contain"
                        />
                    </View>

                    <View style={styles.imageContainer}>
                        <Image
                            source={images.welcome}
                            style={styles.welcomeImage}
                            resizeMode="contain"
                        />
                    </View>
                </View>
            </ImageBackground>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#D1EA28',
    },
    backgroundImage: {
        flex: 1,
        width: '100%',
        height: '100%',
    },
    overlay: {
        flex: 1,
        backgroundColor: 'rgba(0,0,0,0.1)', // Subtle overlay if needed
    },
    content: {
        flex: 1,
        paddingHorizontal: 30,
        paddingTop: 80,
        alignItems: 'center',
        zIndex: 10,
    },
    title: {
        fontSize: 26,
        fontWeight: '500',
        color: '#000000',
        textAlign: 'center',
        lineHeight: 35,
    },
    subtitle: {
        fontSize: 26,
        fontWeight: '400',
    },
    brand: {
        fontSize: 25,
        fontWeight: '900',
    },
    description: {
        fontSize: 18,
        color: '#1A1A1A',
        textAlign: 'center',
        marginTop: 20,
        paddingHorizontal: 20,
        lineHeight: 26,
    },
    logo: {
        width: 70,
        height: 70,
        marginTop: 20,
    },
    imageContainer: {
        position: 'absolute',
        bottom: 0,
        width: width,
        height: height * 0.55,
        justifyContent: 'flex-end',
    },
    welcomeImage: {
        width: '100%',
        height: '100%',
        marginBottom: -50,
    },
});

export default WelcomeScreen;
