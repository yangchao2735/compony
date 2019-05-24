var rollScreen = {
    props:{
        height:{
            default:60,
            type:Number
        },
        lineNum:{
            default:3,
            type:Number
        },
        notice:{
            default:function(){
                return [
                    "我们来自同一个世界就发哦到附近",
                    "我们都有爱的就发哦的及哦啊接发减肥减肥骄傲",
                    "laizjfofjaodjfoajfdo"
                ]
            },
            type:Array
        }
    },
    data() {
        return {
            num: 0
        }
    },
    computed: {
        transform() {
            return "transform:translateY(-" + this.num * this.height + "px);";
        }
    },
    template:
    `<div :style="{height:60 + 'px'}" class="rollScreen_container" id="rollScreen_container">
        <div class="nav">
            <div class="nav_left">
                <img src="@/assets/laba.png" alt="">
                <span>新媒公告</span>
            </div>
            <div class="nav_right">
                <ul class="nav_ct" :style="transform" v-if="notice.length>0">
                <li v-for="(item,index) in notice" :key="index">{{item}}</li>
                <li>{{notice[0]}}</li>
                </ul>
            </div>
        </div>
    </div>`,
    created() {
        this.init();
    },
    methods: {
        init() {
            let that = this;
            that.num += 1;
            let timeId = setInterval(function() {
                if (that.num == that.notice.length) { //判断num是否大于或等于notice的长度，让num为0，回到七点
                that.num = 0;
                clearInterval(timeId);  //清除定时器，防止时间停留两个3秒
                that.init();
                } else {
                that.num += 1;
                }
            }, 3000);
        }
    }
}
export default {rollScreen}