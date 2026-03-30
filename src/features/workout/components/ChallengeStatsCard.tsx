import { COLORS } from '@/constants/colors';
import { Clock, Trophy } from 'lucide-react-native';
import React from 'react';
import { StyleSheet, Text, View } from 'react-native';

const ChallengeStatsCard = () => {
    return (
        <View style={styles.container}>
            <Text style={styles.title}>Défi 7x4</Text>

            <View style={styles.statsContainer}>
                <View style={styles.statItem}>
                    <Clock size={28} color="#000000" />
                    <View style={styles.statTextContainer}>
                        <Text style={styles.statLabel}>Temps</Text>
                        <Text style={styles.statValue}>00:04:30</Text>
                    </View>
                </View>

                <View style={styles.divider} />

                <View style={styles.statItem}>
                    <Trophy size={28} color="#000000" />
                    <View style={styles.statTextContainer}>
                        <Text style={styles.statLabel}>Progression</Text>
                        <Text style={styles.statValue}>0%</Text>
                    </View>
                </View>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        position: 'absolute',
        top: -30, // Floating half-on, half-off
        width: '90%',
        alignSelf: 'center',
        backgroundColor: COLORS.primary,
        borderRadius: 24,
        padding: 24,
        elevation: 12,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 8 },
        shadowOpacity: 0.25,
        shadowRadius: 10,
    },
    title: {
        fontSize: 16,
        fontWeight: '700',
        color: '#000000',
        marginBottom: 12,
        opacity: 0.8,
    },
    statsContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
    },
    statItem: {
        flexDirection: 'row',
        alignItems: 'center',
        flex: 1,
    },
    statTextContainer: {
        marginLeft: 12,
    },
    statLabel: {
        fontSize: 11,
        fontWeight: '600',
        color: '#000000',
        opacity: 0.6,
        textTransform: 'uppercase',
    },
    statValue: {
        fontSize: 17,
        fontWeight: '900',
        color: '#000000',
    },
    divider: {
        width: 1,
        height: 30,
        backgroundColor: '#000000',
        opacity: 0.15,
        marginHorizontal: 10,
    },
});

export default ChallengeStatsCard;
