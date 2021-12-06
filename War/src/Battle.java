
public class Battle {

	public static void main(String[] args) {
		Soldier commando=new Soldier(12,150);
		Warrior conan=new Warrior(15,120);
		
		System.out.println(Math.random());
		System.out.println("War started.");
		while(commando.getHP()>0 && 
				conan.getHP()>0) {
			try {
				// 0��1 ���߿� �ϳ��� �����߻���Ű�� �ڵ�.
				commando.attack(conan);
				Thread.sleep(500);
				conan.attack(commando);
				Thread.sleep(500);
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
			
		}
		if(commando.getHP()>conan.getHP()) {
			System.out.println("�ڸ����� �̰���ϴ�.(���� ��)");
		} else if(commando.getHP()<conan.getHP()) {
			System.out.println("�ڳ��� �̰���ϴ�.(���� ��)");
		} else {
			System.out.println("�Ѵ� �׾����ϴ�.");
		}
		
	}

}