import React from 'react';
import { FlatList, StyleSheet, Text, View } from 'react-native';
import ChallengeCard from './ChallengeCard';

// Dummy data mirroring the provided design requirements
const DUMMY_CHALLENGES = [
    {
        id: '1',
        title: 'Défi Corps Complet',
        duration: '30 jours',
        description: 'Lorem ipsum dolor sit amet consectetur adipiscing eli',
        // Fallback to workout1.jpg or habillage as requested placeholder if actual image is missing
        imageSource: require('@/assets/images/workout1.jpg'),
    },
    {
        id: '2',
        title: 'Défi Abdos Béton',
        duration: '21 jours',
        description: 'Forge tes abdominaux avec ce programme intensif de 3 semaines.',
        imageSource: require('@/assets/images/workout1.jpg'),
    },
    {
        id: '3',
        title: 'Défi Perte Express',
        duration: '14 jours',
        description: 'Brûle un maximum de calories en un temps record.',
        imageSource: require('@/assets/images/workout1.jpg'),
    },
];

const ChallengesSection = () => {
    return (
        <View style={styles.container}>
            <View style={styles.headerContainer}>
                <Text style={styles.title}>Défis</Text>
            </View>

            <FlatList
                data={DUMMY_CHALLENGES}
                keyExtractor={(item) => item.id}
                horizontal
                showsHorizontalScrollIndicator={false}
                contentContainerStyle={styles.listContent}
                snapToAlignment="start"
                decelerationRate="fast"
                // The item interval snap: CARD_WIDTH (width * 0.75) + margin (20)
                // This will be calculated effectively by FlatList snapping natively
                snapToInterval={0 /* We will rely on paging or standard scrolling if custom interval is tricky without exact layout metrics, but standard native horizontal scroll is often enough */}
                renderItem={({ item }) => (
                    <ChallengeCard
                        title={item.title}
                        duration={item.duration}
                        description={item.description}
                        imageSource={item.imageSource}
                        onPress={() => console.log('Challenge pressed:', item.id)}
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
    headerContainer: {
        paddingHorizontal: 20,
        marginBottom: 20,
        borderTopWidth: 1,
        borderTopColor: '#333333', // Subtle separator above the title
        paddingTop: 20,
    },
    title: {
        color: '#FFFFFF',
        fontSize: 18,
        fontWeight: 'bold',
    },
    listContent: {
        paddingHorizontal: 20, // Initial padding so first card aligns with header
        paddingRight: 40, // Extra padding at the end for scroll bounce
    },
});

export default ChallengesSection;
