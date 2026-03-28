import React from 'react';
import { StyleSheet, Text, View } from 'react-native';
import { images } from '../constants/images';
import TargetedExerciseCard from './TargetedExerciseCard';

const TargetedExerciseSection = () => {
    return (
        <View style={styles.container}>
            <Text style={styles.sectionTitle}>Exercices ciblés</Text>

            <View style={styles.grid}>
                <TargetedExerciseCard
                    title="Poitrine"
                    imageSource={images.poitrine}
                    onPress={() => console.log('Poitrine clicked')}
                />
                <TargetedExerciseCard
                    title="Bras & épaule"
                    imageSource={images.bras}
                    onPress={() => console.log('Bras & épaule clicked')}
                />
                <TargetedExerciseCard
                    title="Abdos"
                    imageSource={images.abdos}
                    onPress={() => console.log('Abdos clicked')}
                />
                <TargetedExerciseCard
                    title="Fessier"
                    imageSource={images.fessier}
                    onPress={() => console.log('Fessier clicked')}
                />
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        paddingHorizontal: 20,
        marginTop: 10,
        marginBottom: 20,
    },
    sectionTitle: {
        color: '#FFFFFF',
        fontSize: 20,
        fontWeight: 'bold',
        marginBottom: 15,
    },
    grid: {
        flexDirection: 'row',
        flexWrap: 'wrap',
        justifyContent: 'space-between',
    },
});

export default TargetedExerciseSection;
