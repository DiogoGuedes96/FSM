import moment from "moment";

const formatDatePt = (date) => {
    const newDate = moment(date).format("DD/MM/YYYY") ?? "";

    return newDate;
};

export { formatDatePt };
