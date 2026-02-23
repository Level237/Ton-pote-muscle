import { COLORS } from '@/constants/colors';
import React from 'react';
import { StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
interface FooterGoalProps {
    onPress: () => void;
    title?: string;
    disabled?: boolean;
}

const FooterGoal = ({ onPress, title = 'Suivant', disabled = false }: FooterGoalProps) => {
    const insets = useSafeAreaInsets();

    return (
        <View style={[styles.container, { paddingBottom: insets.bottom + 20 }]}>
            <TouchableOpacity
                style={[styles.button, disabled && styles.disabledButton]}
                onPress={onPress}
                disabled={disabled}
                activeOpacity={0.8}
            >
                <Text style={styles.buttonText}>{title}</Text>
            </TouchableOpacity>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        width: '100%',
        paddingHorizontal: 25,
        backgroundColor: '#FFFFFF',
        paddingTop: 10,
    },
    button: {
        backgroundColor: COLORS.primary,
        height: 56,
        borderRadius: 0, // Rectangular as per screenshot
        justifyContent: 'center',
        alignItems: 'center',
        width: '100%',
    },
    disabledButton: {
        backgroundColor: '#E0E0E0',
    },
    buttonText: {
        fontSize: 18,
        fontWeight: '700',
        color: '#000000',
    },
});

export default FooterGoal;
