import { COLORS } from '@/constants/colors';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import React from 'react';
import { StyleSheet, Text, View } from 'react-native';

const WeeklyRecap = () => {
    return (
        <View style={styles.container}>
            {/* Recap Section */}
            <View style={styles.recapHeader}>
                <Text style={styles.recapTitle}>Ton recap de la semaine</Text>
                <Text style={styles.recapLevel}>Nv. 0</Text>
            </View>

            {/* Stats Card */}
            <View style={styles.statsCard}>
                <View style={styles.statCol}>
                    <MaterialCommunityIcons name="medal-outline" size={32} color="#000000" />
                    <Text style={styles.statValue}>0</Text>
                    <Text style={styles.statLabel}>Exercices</Text>
                </View>
                <View style={styles.statCol}>
                    <MaterialCommunityIcons name="fire" size={32} color="#000000" />
                    <Text style={styles.statValue}>0</Text>
                    <Text style={styles.statLabel}>Kcal</Text>
                </View>
                <View style={styles.statCol}>
                    <MaterialCommunityIcons name="clock-outline" size={32} color="#000000" />
                    <Text style={styles.statValue}>0</Text>
                    <Text style={styles.statLabel}>minutes</Text>
                </View>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        width: '100%',
        paddingHorizontal: 20,
        backgroundColor: '#000000',
        marginTop: 20,
    },
    recapHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-end',
        marginBottom: 15,
    },
    recapTitle: {
        color: '#FFFFFF',
        fontSize: 18,
        fontWeight: '500',
    },
    recapLevel: {
        color: '#FFFFFF',
        fontSize: 22,
        fontWeight: 'bold',
    },
    statsCard: {
        backgroundColor: COLORS.primary,
        borderRadius: 15,
        flexDirection: 'row',
        justifyContent: 'space-between',
        paddingVertical: 20,
        paddingHorizontal: 30,
        marginBottom: 20,
    },
    statCol: {
        alignItems: 'center',
    },
    statValue: {
        color: '#000000',
        fontSize: 20,
        fontWeight: 'bold',
        marginTop: 5,
        marginBottom: 2,
    },
    statLabel: {
        color: '#000000',
        fontSize: 12,
        fontWeight: '500',
    },
});

export default WeeklyRecap;
