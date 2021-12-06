
public class Calculator {
	final static double pi=3.14159;   // 常數(constant number)
	// static: instance생성없이 사용할 수 있는 전역변수(필드)/함수(메소드)
	// final: 변경불가 (변수->상수)
	static String company="LG";
	static String model="LCD"; 
	static String info;
	static {
		info=company+"-"+model;
	}
	static int plus(int x, int y) {
		return x+y;
	}
	static int minus(int x, int y) {
		return x-y;
	}
}
