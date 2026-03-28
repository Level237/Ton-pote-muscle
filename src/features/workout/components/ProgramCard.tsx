import React from 'react';
import { Dimensions, ImageBackground, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

const { width } = Dimensions.get('window');
const CARD_WIDTH = width * 0.42;
const CARD_HEIGHT = CARD_WIDTH * 1.2;

interface ProgramCardProps {
    title: string;
    imageSource: any;
    onPress?: () => void;
}

const ProgramCard: React.FC<ProgramCardProps> = ({ title, imageSource, onPress }) => {
    return (
        <TouchableOpacity style={styles.card} activeOpacity={0.9} onPress={onPress}>
            <ImageBackground
                source={imageSource}
                style={styles.imageBackground}
                imageStyle={styles.imageStyle}
            >
                <View style={styles.overlay}>
                    <Text style={styles.title}>{title}</Text>
                </View>
            </ImageBackground>
        </TouchableOpacity>
    );
};

const styles = StyleSheet.create({
    card: {
        width: CARD_WIDTH,
        height: CARD_HEIGHT,
        borderRadius: 15,
        marginRight: 15,
        overflow: 'hidden',
    },
    imageBackground: {
        flex: 1,
        justifyContent: 'flex-end',
    },
    imageStyle: {
        borderRadius: 15,
    },
    overlay: {
        height: '40%',
        backgroundColor: 'rgba(0,0,0,0.5)',
        justifyContent: 'center',
        paddingHorizontal: 12,
    },
    title: {
        color: '#F4D03F', // Slightly lighter yellow for readability on images
        fontSize: 14,
        fontWeight: 'bold',
    },
});

export default ProgramCard;
