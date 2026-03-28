import { programmes } from '@/features/workout/constants/images';
import React from 'react';
import { FlatList, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import ProgramCard from './ProgramCard';

const ProgramSection = () => {
    const listData = [
        { id: '1', title: 'Echauffement', image: programmes.programme1 },
        { id: '2', title: 'Etirements', image: programmes.programme2 },
        { id: '3', title: 'Soulagement', image: programmes.programme3 },
        { id: '4', title: 'Onboarding', image: programmes.programme4 },
    ];

    return (
        <View style={styles.container}>
            <View style={styles.header}>
                <Text style={styles.title}>Etirements et échauffements</Text>
                <TouchableOpacity>
                    <Text style={styles.link}>Voir plus</Text>
                </TouchableOpacity>
            </View>

            <FlatList
                data={listData}
                keyExtractor={(item) => item.id}
                horizontal
                showsHorizontalScrollIndicator={false}
                contentContainerStyle={styles.listContent}
                renderItem={({ item }) => (
                    <ProgramCard
                        title={item.title}
                        imageSource={item.image}
                        onPress={() => console.log('Program clicked:', item.title)}
                    />
                )}
            />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        marginTop: 10,
        marginBottom: 30,
    },
    header: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingHorizontal: 20,
        marginBottom: 15,
    },
    title: {
        color: '#FFFFFF',
        fontSize: 18,
        fontWeight: 'bold',
    },
    link: {
        color: '#F4D03F',
        fontSize: 14,
        fontWeight: '500',
    },
    listContent: {
        paddingHorizontal: 20,
    },
});

export default ProgramSection;
