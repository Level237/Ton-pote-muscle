import HeaderWorkout from '@/features/workout/components/HeaderWorkout';
import React from 'react';
import { ScrollView, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

export default function HomeScreen() {
    const insets = useSafeAreaInsets();

    return (
        <ScrollView style={[styles.container, { paddingTop: insets.top }]}>
            <HeaderWorkout />
            <View style={styles.content}>
                <Text style={styles.title}>Accueil Contenu</Text>
            </View>
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#000000',
    },
    content: {
        alignItems: 'center',
        justifyContent: 'center',
        marginTop: 40,
    },
    title: {
        fontSize: 24,
        fontWeight: 'bold',
        color: '#FFFFFF',
    },
});
