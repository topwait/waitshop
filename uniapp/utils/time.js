/**
 * 格式化日期格式 (用于兼容ios Date对象)
 */
export const formatDate = (time) => {
  // 将xxxx-xx-xx的时间格式，转换为 xxxx/xx/xx的格式 
  return time.replace(/\-/g, "/");
}

/**
 * 时间戳转日期格式
 * @param {Object} timeStamp
 */
export const timeStampTurnTime(timeStamp, type = "") {
	if (timeStamp != undefined && timeStamp != "" && timeStamp > 0) {
		var date = new Date();
		date.setTime(timeStamp * 1000);
		var y = date.getFullYear();
		var m = date.getMonth() + 1;
		m = m < 10 ? ('0' + m) : m;
		var d = date.getDate();
		d = d < 10 ? ('0' + d) : d;
		var h = date.getHours();
		h = h < 10 ? ('0' + h) : h;
		var minute = date.getMinutes();
		var second = date.getSeconds();
		minute = minute < 10 ? ('0' + minute) : minute;
		second = second < 10 ? ('0' + second) : second;
		if (type) {
			return y + '-' + m + '-' + d;
		} else {
			return y + '-' + m + '-' + d + ' ' + h + ':' + minute + ':' + second;
		}

	} else {
		return "";
	}
}

/**
 * 日期格式转时间戳
 * @param {Object} timeStamp
 */
export const timeTurnTimeStamp(string) {
	var f = string.split(' ', 2);
	var d = (f[0] ? f[0] : '').split('-', 3);
	var t = (f[1] ? f[1] : '').split(':', 3);
	return (new Date(
		parseInt(d[0], 10) || null,
		(parseInt(d[1], 10) || 1) - 1,
		parseInt(d[2], 10) || null,
		parseInt(t[0], 10) || null,
		parseInt(t[1], 10) || null,
		parseInt(t[2], 10) || null
	)).getTime() / 1000;
}

/**
 * 获取某天日期
 * @param {Object} day
 */
export const getDay(day) {
	var today = new Date();
	var targetday_milliseconds = today.getTime() + 1000 * 60 * 60 * 24 * day;
	today.setTime(targetday_milliseconds);

	const doHandleMonth = function(month) {
		var m = month;
		if (month.toString().length == 1) {
			m = "0" + month;
		}
		return m
	}

	var tYear = today.getFullYear();
	var tMonth = today.getMonth();
	var tDate = today.getDate();
	var tWeek = today.getDay();
	var time = parseInt(today.getTime() / 1000);
	tMonth = doHandleMonth(tMonth + 1);
	tDate = doHandleMonth(tDate);

	const week = ['周日', '周一', '周二', '周三', '周四', '周五', '周六']
	return {
		't': time,
		'y': tYear,
		'm': tMonth,
		'd': tDate,
		'w': week[tWeek]
	};
}

/**
 * 对Date的扩展，将 Date 转化为指定格式的String
 * 月(Y)、月(m)、日(d)、小时(H)、分(M)、秒(S) 可以用 1-2 个占位符，
 * 例子：
 * dateFormat('YYYY-mm-dd HH:MM:SS', new Date()) ==> 2020-01-01 08:00:00
 */
export const dateFormat = (fmt, date) => {
  const opt = {
    "Y+": date.getFullYear().toString(), // 年
    "m+": (date.getMonth() + 1).toString(), // 月
    "d+": date.getDate().toString(), // 日
    "H+": date.getHours().toString(), // 时
    "M+": date.getMinutes().toString(), // 分
    "S+": date.getSeconds().toString() // 秒
    // 有其他格式化字符需求可以继续添加，必须转化成字符串
  };
  let ret
  for (let k in opt) {
    ret = new RegExp("(" + k + ")").exec(fmt)
    if (ret) {
      fmt = fmt.replace(ret[1], (ret[1].length == 1) ? (opt[k]) : (opt[k].padStart(ret[1].length, "0")))
    };
  };
  return fmt
}

/**
 * 倒计时
 * @param {Object} seconds 秒
 */
export const countDown(seconds) {
	let [day, hour, minute, second] = [0, 0, 0, 0]
	if (seconds > 0) {
		day = Math.floor(seconds / (60 * 60 * 24))
		hour = Math.floor(seconds / (60 * 60)) - (day * 24)
		minute = Math.floor(seconds / 60) - (day * 24 * 60) - (hour * 60)
		second = Math.floor(seconds) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60)
	}
	if (day < 10) {
		day = '0' + day
	}
	if (hour < 10) {
		hour = '0' + hour
	}
	if (minute < 10) {
		minute = '0' + minute
	}
	if (second < 10) {
		second = '0' + second
	}
	return {
		d: day,
		h: hour,
		i: minute,
		s: second
	};
}