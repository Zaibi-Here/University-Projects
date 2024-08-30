import mongoose from "mongoose";

const overAllStatSchema = new mongoose.Schema(
  {
    totalCustomer: Number,
    yearlySalesTotal: Number,
    yearlyTotalSoldUnits: Number,
    year: Number,
    monthlyData: [
      {
        month: String,
        totalSales: Number,
        totalUnits: Number,
      },
    ],
    dailyData: [{
      date: String,
      totalSales: Number,
      totalUnits: Number,
    }],
    salesByCategory: {
      type: Map,
      of: Number,
    },
  },
  { timestamps: true }
);

const OverallStat = new mongoose.model("OverallStat", overAllStatSchema);

export default OverallStat;
