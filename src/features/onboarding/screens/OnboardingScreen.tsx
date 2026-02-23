import { images as globalImages } from '@/constants/images';
import { useRouter } from 'expo-router';
import React, { useRef } from 'react';
import {
    Dimensions,
    Image,
    ImageBackground,
    StyleSheet,
    Text,
    TouchableOpacity,
    View,
} from 'react-native';
import Animated, {
    Extrapolation,
    interpolate,
    useAnimatedScrollHandler,
    useAnimatedStyle,
    useSharedValue,
    type SharedValue,
} from 'react-native-reanimated';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { ONBOARDING_DATA, OnboardingData } from '../data/onboarding';

const { width, height } = Dimensions.get('window');

interface OnboardingSlideProps {
    item: OnboardingData;
    index: number;
    scrollX: SharedValue<number>;
}

const OnboardingSlide = ({ item, index, scrollX }: OnboardingSlideProps) => {
    const animatedContentStyle = useAnimatedStyle(() => {
        const opacity = interpolate(
            scrollX.value,
            [(index - 0.5) * width, index * width, (index + 0.5) * width],
            [0, 1, 0],
            Extrapolation.CLAMP
        );
        return { opacity };
    });

    return (
        <View style={styles.slide}>
            <ImageBackground
                source={item.image}
                style={styles.backgroundImage}
                resizeMode="cover"
            >
                <Image
                    source={item.background}
                    style={styles.decorationImage}
                    resizeMode="stretch"
                />

                <Animated.View style={[styles.contentOverlay, animatedContentStyle]}>
                    <View style={styles.textContent}>
                        <Text style={styles.title}>{item.title}</Text>
                    </View>
                </Animated.View>
            </ImageBackground>
        </View>
    );
};

interface DotProps {
    index: number;
    scrollX: SharedValue<number>;
}

const PaginationDot = ({ index, scrollX }: DotProps) => {
    const dotStyle = useAnimatedStyle(() => {
        const dotWidth = interpolate(
            scrollX.value,
            [(index - 1) * width, index * width, (index + 1) * width],
            [10, 25, 10],
            Extrapolation.CLAMP
        );
        const opacity = interpolate(
            scrollX.value,
            [(index - 1) * width, index * width, (index + 1) * width],
            [0.4, 1, 0.4],
            Extrapolation.CLAMP
        );
        return {
            width: dotWidth,
            opacity,
        };
    });

    return <Animated.View style={[styles.dot, dotStyle]} />;
};

interface ButtonTextProps {
    index: number;
    scrollX: SharedValue<number>;
}

const AnimatedButtonText = ({ index, scrollX }: ButtonTextProps) => {
    const textStyle = useAnimatedStyle(() => {
        const opacity = interpolate(
            scrollX.value,
            [(index - 0.5) * width, index * width, (index + 0.5) * width],
            [0, 1, 0],
            Extrapolation.CLAMP
        );
        return {
            opacity,
        };
    });

    return (
        <Animated.Text
            style={[
                styles.buttonText,
                { position: index === 0 ? 'relative' : 'absolute' } as any,
                textStyle,
            ]}
        >
            {index === ONBOARDING_DATA.length - 1 ? 'Terminer' : 'Sauter'}
        </Animated.Text>
    );
};

interface OnboardingScreenProps {
    onComplete?: () => void;
}

const OnboardingScreen = ({ onComplete }: OnboardingScreenProps) => {
    const insets = useSafeAreaInsets();
    const router = useRouter();
    const scrollX = useSharedValue(0);
    const flatListRef = useRef<Animated.FlatList<OnboardingData>>(null);

    const scrollHandler = useAnimatedScrollHandler({
        onScroll: (event) => {
            scrollX.value = event.contentOffset.x;
        },
    });

    const handleNext = () => {
        const currentIndex = Math.round(scrollX.value / width);
        if (currentIndex < ONBOARDING_DATA.length - 1) {
            flatListRef.current?.scrollToOffset({
                offset: (currentIndex + 1) * width,
                animated: true,
            });
        } else {
            router.push('/(goal)/gender');
            console.log("le")
        }
    };

    return (
        <View style={[styles.container, { paddingTop: insets.top, paddingBottom: insets.bottom }]}>
            {/* Background Slider */}
            <Animated.FlatList
                ref={flatListRef}
                data={ONBOARDING_DATA}
                renderItem={({ item, index }) => (
                    <OnboardingSlide item={item} index={index} scrollX={scrollX} />
                )}
                horizontal
                pagingEnabled
                showsHorizontalScrollIndicator={false}
                onScroll={scrollHandler}
                scrollEventThrottle={16}
                keyExtractor={(item) => item.id.toString()}
            />

            {/* Static UI Layer */}
            <View style={[styles.staticLayer, { paddingTop: insets.top, paddingBottom: insets.bottom }]} pointerEvents="box-none">
                <Image
                    source={globalImages.logo}
                    style={styles.logo}
                    resizeMode="contain"
                />

                <View style={styles.footer} pointerEvents="box-none">
                    <View style={styles.pagination}>
                        {ONBOARDING_DATA.map((_, i) => (
                            <PaginationDot key={i} index={i} scrollX={scrollX} />
                        ))}
                    </View>

                    <TouchableOpacity
                        style={styles.button}
                        onPress={handleNext}
                    >
                        <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                            {ONBOARDING_DATA.map((_, i) => (
                                <AnimatedButtonText key={i} index={i} scrollX={scrollX} />
                            ))}
                        </View>
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#000000',
    },
    staticLayer: {
        ...StyleSheet.absoluteFillObject,
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingHorizontal: 20,
        zIndex: 10,
    },
    slide: {
        width,
        height,
    },
    backgroundImage: {
        flex: 1,
        width,
        height,
    } as any,
    decorationImage: {
        position: 'absolute',
        width: width,
        height: height,
        top: 0,
        left: 0,
    } as any,
    contentOverlay: {
        flex: 1,
        backgroundColor: 'rgba(0,0,0,0.2)',
        justifyContent: 'center',
        alignItems: 'flex-start',
        paddingHorizontal: 20,
    },
    logo: {
        width: 60,
        height: 60,
        marginTop: 20,
    },
    textContent: {
        width: '100%',
        marginTop: height * 0.2, // Position text appropriately
    },
    title: {
        fontSize: 36,
        fontWeight: '900',
        color: '#D1EA28',
        textAlign: 'left',
        lineHeight: 44
    },
    footer: {
        width: '100%',
        alignItems: 'center',
        marginBottom: 48,
    },
    pagination: {
        flexDirection: 'row',
        height: 20,
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: 20,
    },
    dot: {
        height: 10,
        borderRadius: 5,
        marginHorizontal: 5,
        backgroundColor: '#D1EA28',
    },
    button: {
        backgroundColor: '#FFFFFF',
        paddingHorizontal: 20,
        paddingVertical: 12,
        borderRadius: 35,
        width: '55%',
        alignItems: 'center',
        elevation: 5,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.25,
        shadowRadius: 3.84,
    },
    buttonText: {
        fontSize: 18,
        fontWeight: '800',
        color: '#000000',
    },
});

export default OnboardingScreen;
