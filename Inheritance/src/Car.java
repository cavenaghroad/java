
public class Car {
	Tire[] tires= {	
			new Tire(6,"�տ���"),
			new Tire(2,"�տ�����"),
			new Tire(3,"�ڿ���"),
			new Tire(4,"�ڿ�����")
	};

	public Car() {}
	
	void stop() {
		System.out.println("�ڵ����� ����ϴ�.");
	}
	int run() {
		System.out.println("�ڵ����� �޸��ϴ�.");
		
		for(int i=0; i<tires.length; i++) {
			if(!tires[i].roll()) {stop(); return i+1;}
		}
		return 0;
	}
}

