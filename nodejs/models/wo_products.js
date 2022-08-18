/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
    return sequelize.define('Wo_Products', {
        id: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        user_id: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        page_id: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        type: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: ""
        },
        description: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        category: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        price: {
            type: DataTypes.STRING(32),
            allowNull: false,
            defaultValue: "0.00"
        },
        start_date: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        end_date: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        total_rooms: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        type: {
            type: DataTypes.ENUM('0', '1'),
            allowNull: false
        },
        currency: {
            type: DataTypes.STRING(40),
            allowNull: false,
            defaultValue: "USD"
        },
        lng: {
            type: DataTypes.STRING(100),
            allowNull: false,
            defaultValue: "0"
        },
        lat: {
            type: DataTypes.STRING(100),
            allowNull: false,
            defaultValue: "0"
        },
        time: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        active: {
            type: DataTypes.ENUM('0', '1'),
            allowNull: false,
            defaultValue: "0"
        }
    }, {
        sequelize,
        tableName: 'Wo_Products'
    });
};