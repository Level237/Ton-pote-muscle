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
const WEIGHTS = Array.from({ length: 111 }, (_, i) => i + 40); // 40kg to 150kg

interface WeightItemProps {
    weight: number;
    index: number;
    scrollY: any;
}

const WeightItem = ({ weight, index, scrollY }: WeightItemProps) => {
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

        const color = scrollY.value > (index - 0.5) * ITEM_HEIGHT && scrollY.value < (index + 0.5) * ITEM_HEIGHT
            ? '#000000'
            : '#CCCCCC';

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
                <Animated.Text style={[styles.weightText, isSelected]}>
                    {weight}
                </Animated.Text>
                {weight === 115 && ( // Just for the visual reference in screenshot if we want to show 'kg'
                    <View style={styles.unitContainer}>
                        {/* We'll handle 'kg' separately to match the design better */}
                    </View>
                )}
            </Animated.View>
        </View>
    );
};

const WeightGoalScreen = () => {
    const insets = useSafeAreaInsets();
    const router = useRouter();
    const scrollY = useSharedValue(0);
    const [selectedWeight, setSelectedWeight] = useState(115);

    // Initial offset to center weight 115
    const initialIndex = WEIGHTS.indexOf(115);
    const initialOffset = initialIndex * ITEM_HEIGHT;

    const scrollHandler = useAnimatedScrollHandler({
        onScroll: (event) => {
            scrollY.value = event.contentOffset.y;
        },
    });

    const onMomentumScrollEnd = (event: NativeSyntheticEvent<NativeScrollEvent>) => {
        const index = Math.round(event.nativeEvent.contentOffset.y / ITEM_HEIGHT);
        setSelectedWeight(WEIGHTS[index]);
    };

    const handleNext = () => {
        router.push('/(goal)/height');
    };

    return (
        <View style={[styles.container, { paddingTop: insets.top, paddingBottom: insets.bottom }]}>
            <HeaderGoal
                step={1}
                progress={2}
                totalSteps={3}
                title="A propos de votre corps"
            />

            <View style={styles.content}>
                <Text style={styles.title}>Quel est votre poids actuel ?</Text>

                <View style={styles.pickerContainer}>
                    {/* Highlight Bars */}
                    <View style={styles.highlightBar} pointerEvents="none" />
                    <View style={[styles.highlightBar, { top: ITEM_HEIGHT * 3 }]} pointerEvents="none" />

                    {/* Center unit indicator */}
                    <View style={styles.unitWrapper} pointerEvents="none">
                        <Text style={styles.unitText}>kg</Text>
                    </View>

                    <Animated.FlatList
                        data={WEIGHTS}
                        keyExtractor={(item) => item.toString()}
                        renderItem={({ item, index }) => (
                            <WeightItem weight={item} index={index} scrollY={scrollY} />
                        )}
                        onScroll={scrollHandler}
                        scrollEventThrottle={16}
                        snapToInterval={ITEM_HEIGHT}
                        decelerationRate="fast"
                        showsVerticalScrollIndicator={false}
                        contentContainerStyle={{
                            paddingVertical: ITEM_HEIGHT * 2, // Offset for centering
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
        paddingHorizontal: 40,
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
    weightText: {
        fontSize: 64,
        fontWeight: '900',
    },
    unitContainer: {
        marginLeft: 5,
    },
    unitWrapper: {
        position: 'absolute',
        left: '50%',
        top: '50%',
        transform: [{ translateX: 60 }, { translateY: -10 }],
        zIndex: 10,
    },
    unitText: {
        fontSize: 24,
        fontWeight: '900',
        color: COLORS.primary,
    },
    highlightBar: {
        position: 'absolute',
        top: ITEM_HEIGHT * 2,
        width: '60%',
        height: 2,
        backgroundColor: 'transparent', // We don't see bars in screenshot but it helps centering
    },
});

export default WeightGoalScreen;
