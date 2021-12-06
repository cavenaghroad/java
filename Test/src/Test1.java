
public class Test1 {
	public static void main(String[] args) {
		TestOut to = new TestOut();
		
		to.setN(10);		//to.n=10; // set (<-write)
		int k=to.getN();
		System.out.println(k);  // get(<-read)
		
	}
}
