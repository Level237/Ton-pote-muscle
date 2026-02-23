import { COLORS } from '@/constants/colors';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import {
    Dimensions,
    NativeScrollEvent,
    NativeSyntheticEvent,
    StyleSheet,
    Text,
    View,
} from 'react-native';
import Animated, {
    Extrapolation,
    interpolate,
    useAnimatedScrollHandler,
    useAnimatedStyle,
    useSharedValue,
} from 'react-native-reanimated';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import FooterGoal from '../components/FooterGoal';
import HeaderGoal from '../components/HeaderGoal';

const { width, height } = Dimensions.get('window');
const ITEM_HEIGHT = 80;
const VISIBLE_ITEMS = 5;
const HEIGHTS = Array.from({ length: 101 }, (_, i) => (i + 120) / 100); // 120cm to 220cm

interface HeightItemProps {
    heightValue: number;
    index: number;
    scrollY: any;
}

const HeightItem = ({ heightValue, index, scrollY }: HeightItemProps) => {
    const animatedStyle = useAnimatedStyle(() => {
        const inputRange = [
            (index - 2) * ITEM_HEIGHT,
            (index - 1) * ITEM_HEIGHT,
            index * ITEM_HEIGHT,
            (index + 1) * ITEM_HEIGHT,
            (index + 2) * ITEM_HEIGHT,
        ];

        const scale = interpolate(
            scrollY.value,
            inputRange,
            [0.6, 0.8, 1.2, 0.8, 0.6],
            Extrapolation.CLAMP
        );

        const opacity = interpolate(
            scrollY.value,
            inputRange,
            [0.3, 0.5, 1, 0.5, 0.3],
            Extrapolation.CLAMP
        );

        return {
            transform: [{ scale }],
            opacity,
        };
    });

    const isSelected = useAnimatedStyle(() => {
        const active = scrollY.value > (index - 0.5) * ITEM_HEIGHT && scrollY.value < (index + 0.5) * ITEM_HEIGHT;
        return {
            color: active ? '#000000' : '#CCCCCC'
        };
    });

    return (
        <View style={styles.itemContainer}>
            <Animated.View style={[styles.itemContent, animatedStyle]}>
                <Animated.Text style={[styles.heightText, isSelected]}>
                    {heightValue}
                </Animated.Text>
            </Animated.View>
        </View>
    );
};

const HeightGoalScreen = () => {
    const insets = useSafeAreaInsets();
    const router = useRouter();
    const scrollY = useSharedValue(0);
    const [selectedHeight, setSelectedHeight] = useState(175);

    // Initial offset to center height 175
    const initialIndex = HEIGHTS.indexOf(175);

    const scrollHandler = useAnimatedScrollHandler({
        onScroll: (event) => {
            scrollY.value = event.contentOffset.y;
        },
    });

    const onMomentumScrollEnd = (event: NativeSyntheticEvent<NativeScrollEvent>) => {
        const index = Math.round(event.nativeEvent.contentOffset.y / ITEM_HEIGHT);
        setSelectedHeight(HEIGHTS[index]);
    };

    const handleNext = () => {
        // router.push('/(goal)/next_category');
        console.log('Selected Height:', selectedHeight);
    };

    return (
        <View style={[styles.container, { paddingTop: insets.top, paddingBottom: insets.bottom }]}>
            <HeaderGoal
                step={1}
                progress={3}
                totalSteps={3}
                title="A propos de votre corps"
            />

            <View style={styles.content}>
                <Text style={styles.title}>Quelle est votre taille actuelle?</Text>

                <View style={styles.pickerContainer}>
                    {/* Center unit indicator */}
                    <View style={styles.unitWrapper} pointerEvents="none">
                        <Text style={styles.unitText}>m</Text>
                    </View>

                    <Animated.FlatList
                        data={HEIGHTS}
                        keyExtractor={(item) => item.toString()}
                        renderItem={({ item, index }) => (
                            <HeightItem heightValue={item} index={index} scrollY={scrollY} />
                        )}
                        onScroll={scrollHandler}
                        scrollEventThrottle={16}
                        snapToInterval={ITEM_HEIGHT}
                        decelerationRate="fast"
                        showsVerticalScrollIndicator={false}
                        contentContainerStyle={{
                            paddingVertical: ITEM_HEIGHT * 2,
                        }}
                        onMomentumScrollEnd={onMomentumScrollEnd}
                        initialScrollIndex={initialIndex - 2 > 0 ? initialIndex - 2 : 0}
                        getItemLayout={(_, index) => ({
                            length: ITEM_HEIGHT,
                            offset: ITEM_HEIGHT * index,
                            index,
                        })}
                    />
                </View>
            </View>

            <FooterGoal onPress={handleNext} />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#FFFFFF',
    },
    content: {
        flex: 1,
        alignItems: 'center',
        paddingTop: 40,
    },
    title: {
        fontSize: 28,
        width: '80%',
        fontWeight: '900',
        color: '#000000',
        textAlign: 'center',
        marginBottom: 20,
        paddingHorizontal: 20,
    },
    pickerContainer: {
        height: ITEM_HEIGHT * VISIBLE_ITEMS,
        width: '100%',
        justifyContent: 'center',
        alignItems: 'center',
        position: 'relative',
    },
    itemContainer: {
        height: ITEM_HEIGHT,
        width: width,
        justifyContent: 'center',
        alignItems: 'center',
    },
    itemContent: {
        flexDirection: 'row',
        alignItems: 'baseline',
    },
    heightText: {
        fontSize: 64,
        fontWeight: '900',
    },
    unitWrapper: {
        position: 'absolute',
        left: '50%',
        top: '50%',
        transform: [{ translateX: 80 }, { translateY: -10 }],
        zIndex: 10,
    },
    unitText: {
        fontSize: 24,
        fontWeight: '900',
        color: COLORS.primary,
    },
});

export default HeightGoalScreen;
