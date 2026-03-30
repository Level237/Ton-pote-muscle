import { CirclePlay, Trophy } from 'lucide-react-native';
import React from 'react';
import { StyleSheet, Text, View } from 'react-native';

const DayItem = ({ number, isDone = false, isTrophy = false }: { number?: number, isDone?: boolean, isTrophy?: boolean }) => {
    return (
        <View style={styles.dayItemContainer}>
            <View style={[styles.dayCircle, isDone && styles.dayCircleDone]}>
                {isTrophy ? (
                    <Trophy size={24} color="#000000" />
                ) : (
                    <Text style={styles.dayNumber}>{number}</Text>
                )}
            </View>
            {!isTrophy && <CirclePlay size={12} color="#000000" style={styles.playMini} />}
        </View>
    );
};

const WeekItem = ({ title, days }: { title: string, days: number[] }) => {
    return (
        <View style={styles.weekContainer}>
            {/* Timeline Line & Dot */}
            <View style={styles.timelineSidebar}>
                <View style={styles.timelineDot} />
                <View style={styles.timelineLine} />
            </View>

            <View style={styles.weekContent}>
                <Text style={styles.weekTitle}>{title}</Text>

                <View style={styles.daysGrid}>
                    <View style={styles.daysRow}>
                        <DayItem number={1} />
                        <DayItem number={2} />
                        <DayItem number={3} />
                        <DayItem number={4} />
                    </View>
                    <View style={styles.daysRow}>
                        <DayItem number={5} />
                        <DayItem number={6} />
                        <DayItem number={7} />
                        <DayItem isTrophy />
                    </View>
                </View>
            </View>
        </View>
    );
};

const ChallengeTimeline = () => {
    const WEEKS = ["Semaine 1", "Semaine 2", "Semaine 3", "Semaine 4"];

    return (
        <View style={styles.container}>
            {WEEKS.map((week, index) => (
                <WeekItem key={index} title={week} days={[1, 2, 3, 4, 5, 6, 7]} />
            ))}
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        width: '100%',
        marginTop: 40,
    },
    weekContainer: {
        flexDirection: 'row',
        marginBottom: 10,
    },
    timelineSidebar: {
        width: 30,
        alignItems: 'center',
    },
    timelineDot: {
        width: 14,
        height: 14,
        borderRadius: 7,
        backgroundColor: '#000000',
        zIndex: 1,
    },
    timelineLine: {
        width: 2,
        flex: 1,
        backgroundColor: '#000000',
        opacity: 0.2,
        marginVertical: -5,
    },
    weekContent: {
        flex: 1,
        paddingBottom: 20,
    },
    weekTitle: {
        fontSize: 22,
        fontWeight: '900',
        color: '#000000',
        marginBottom: 15,
        marginTop: -5,
    },
    daysGrid: {
        width: '100%',
    },
    daysRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginBottom: 10,
    },
    dayItemContainer: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    dayCircle: {
        width: 50,
        height: 50,
        borderRadius: 25,
        backgroundColor: '#C0C0C0',
        justifyContent: 'center',
        alignItems: 'center',
    },
    dayCircleDone: {
        backgroundColor: '#C0C0C0', // Could be yellow if completed
    },
    dayNumber: {
        fontSize: 18,
        fontWeight: '900',
        color: '#000000',
    },
    playMini: {
        marginLeft: 4,
    },
});

export default ChallengeTimeline;
