
public class Car {
	Tire[] tires= {	
			new Tire(6,"앞왼쪽"),
			new Tire(2,"앞오른쪽"),
			new Tire(3,"뒤왼쪽"),
			new Tire(4,"뒤오른쪽")
	};

	public Car() {}
	
	void stop() {
		System.out.println("자동차가 멈춥니다.");
	}
	int run() {
		System.out.println("자동차가 달립니다.");
		
		for(int i=0; i<tires.length; i++) {
			if(!tires[i].roll()) {stop(); return i+1;}
		}
		return 0;
	}
}

