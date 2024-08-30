import mongoose from "mongoose";

const productStatSchema = new mongoose.Schema({

    productId:String,
    yearlySalesTotal:String,
    yearlyTotalSoldUnits:String,
    year:Number,
    monthlyData:[{
        month:String,
        totalSales:Number,
        totalUnits:Number,
    },],
    dailyData:[{

        date:String,
        totalSales:Number,
        totalUnits:Number,
    }],
}, { timestamps: true });

const ProductStat = new mongoose.model("ProductStat", productStatSchema);

export default ProductStat;
